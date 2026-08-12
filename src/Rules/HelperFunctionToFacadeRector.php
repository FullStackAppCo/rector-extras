<?php

declare(strict_types=1);

namespace FullStackAppCo\RectorExtras\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Converts Laravel's global helper functions to their equivalent facade calls,
 * e.g. `config('app.name')` becomes `Config::get('app.name')`.
 */
class HelperFunctionToFacadeRector extends AbstractRector
{
    /**
     * Helpers that, when called with no arguments, return the object behind a
     * facade. A chained method call is rewritten as a static call on the facade.
     *
     * @var array<string, string>
     */
    protected const array CHAINED = [
        'app' => 'App',
        'auth' => 'Auth',
        'cache' => 'Cache',
        'config' => 'Config',
        'cookie' => 'Cookie',
        'logger' => 'Log',
        'redirect' => 'Redirect',
        'request' => 'Request',
        'response' => 'Response',
        'session' => 'Session',
        'url' => 'URL',
        'view' => 'View',
    ];

    /**
     * Helpers whose argument form maps directly to a single facade method.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    protected const array SHORTHAND = [
        'app' => ['App', 'make'],
        'broadcast' => ['Broadcast', 'event'],
        'cookie' => ['Cookie', 'make'],
        'event' => ['Event', 'dispatch'],
        'info' => ['Log', 'info'],
        'logger' => ['Log', 'debug'],
        'redirect' => ['Redirect', 'to'],
        'request' => ['Request', 'input'],
        'response' => ['Response', 'make'],
        'url' => ['URL', 'to'],
        'validator' => ['Validator', 'make'],
        'view' => ['View', 'make'],
    ];

    protected const string FACADE_NAMESPACE = 'Illuminate\Support\Facades\\';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert Laravel global helper functions to their equivalent facade calls',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$name = config('app.name');
$user = auth()->user();
CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
$name = Config::get('app.name');
$user = Auth::user();
CODE_SAMPLE,
                ),
            ],
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class, MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->refactorMethodCall($node);
        }

        if ($node instanceof FuncCall) {
            return $this->refactorFuncCall($node);
        }

        return null;
    }

    /**
     * Rewrite `helper()->method(...)` as `Facade::method(...)`.
     */
    protected function refactorMethodCall(MethodCall $methodCall): ?StaticCall
    {
        if ($methodCall->isFirstClassCallable()) {
            return null;
        }

        $funcCall = $methodCall->var;

        if (! $funcCall instanceof FuncCall || $funcCall->isFirstClassCallable() || $funcCall->getArgs() !== []) {
            return null;
        }

        $name = $this->getName($funcCall);

        if ($name === null || ! isset(self::CHAINED[$name]) || ! $methodCall->name instanceof Identifier) {
            return null;
        }

        return $this->toStaticCall(self::CHAINED[$name], $methodCall->name->toString(), $methodCall->args);
    }

    /**
     * Rewrite the argument form of a helper, e.g. `config('key')` as `Config::get('key')`.
     */
    protected function refactorFuncCall(FuncCall $funcCall): ?StaticCall
    {
        if ($funcCall->isFirstClassCallable()) {
            return null;
        }

        $name = $this->getName($funcCall);
        $args = $funcCall->getArgs();

        if ($name === null || $args === []) {
            return null;
        }

        $firstArgumentIsArray = $args[0]->value instanceof Array_;

        return match ($name) {
            'config' => $this->toStaticCall('Config', $firstArgumentIsArray ? 'set' : 'get', $funcCall->args),
            'cache' => $firstArgumentIsArray ? null : $this->toStaticCall('Cache', 'get', $funcCall->args),
            'session' => $firstArgumentIsArray ? null : $this->toStaticCall('Session', 'get', $funcCall->args),
            default => isset(self::SHORTHAND[$name])
                ? $this->toStaticCall(self::SHORTHAND[$name][0], self::SHORTHAND[$name][1], $funcCall->args)
                : null,
        };
    }

    /**
     * @param  array<int, Node\Arg|Node\VariadicPlaceholder>  $args
     */
    protected function toStaticCall(string $facade, string $method, array $args): StaticCall
    {
        return new StaticCall(
            new FullyQualified(self::FACADE_NAMESPACE.$facade),
            new Identifier($method),
            $args,
        );
    }
}
