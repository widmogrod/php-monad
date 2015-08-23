<?php
namespace Functional;

use Monad;
use Common;
use FantasyLand;

/**
 * Append array with values.
 *
 * @param array $array
 * @param array $values
 * @return array
 */
function push(array $array, array $values)
{
    foreach ($values as $value) {
        $array[] = $value;
    }

    return $array;
}

/**
 * Return new array comprised of values from first array, and value from second value or array.
 *
 * @param array $array
 * @param mixed|array $value
 * @return array
 */
function concat(array $array, $value)
{
    if ($value instanceof Common\ConcatInterface) {
        return $value->concat($array);
    }

    if (is_array($value)) {
        return push($array, $value);
    }
    $array[] = $value;

    return $array;
}

const identity = 'Functional\identity';

/**
 * Return value passed to function
 *
 * @param mixed $x
 * @return mixed
 */
function identity($x = null)
{
    return $x;
}

/**
 * Curry function
 *
 * @param integer $numberOfArguments
 * @param callable $function
 * @param array $args
 * @return callable
 */
function curryN($numberOfArguments, callable $function, array $args = [])
{
    return function () use ($numberOfArguments, $function, $args) {
        $argsLeft = $numberOfArguments - func_num_args();

        return $argsLeft <= 0
            ? call_user_func_array($function, push($args, func_get_args()))
            : curryN($argsLeft, $function, push($args, func_get_args()));
    };
}

/**
 * Curry function
 *
 * @param callable $function
 * @param array $args
 * @return callable
 */
function curry(callable $function, array $args = [])
{
    $reflectionOfFunction = new \ReflectionFunction($function);

    $numberOfArguments = count($reflectionOfFunction->getParameters());
    // We cant expect more arguments than are defined in function
    // So if some arguments are provided on start, umber of arguments to curry should be reduces
    $numberOfArguments -= count($args);

    return curryN($numberOfArguments, $function, $args);
}

const valueOf = 'Functional\valueOf';

/**
 * Retrieve value of a object
 *
 * @param Common\ValueOfInterface|mixed $value
 * @return mixed
 */
function valueOf($value)
{
    return $value instanceof Common\ValueOfInterface
        ? $value->extract()
        : $value;
}

const tee = 'Functional\tee';

/**
 * Call $function with $value and return $value
 *
 * @param callable $function
 * @param mixed $value
 * @return \Closure
 */
function tee(callable $function = null, $value = null)
{
    return call_user_func_array(curryN(2, function (callable $function, $value) {
        call_user_func($function, $value);

        return $value;
    }), func_get_args());
}

const compose = 'Functional\compose';

/**
 * Compose multiple functions into one.
 * Composition starts from right to left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'abc'
 * strtolower(strtouppser('aBc'))  ≡ 'abc'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($value) : mixed
 */
function compose(callable $a, callable $b)
{
    return call_user_func_array(
        reverse('Functional\pipeline'),
        func_get_args()
    );
}

const pipeline = 'Functional\pipeline';

/**
 * Compose multiple functions into one.
 * Composition starts from left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'ABC'
 * strtouppser(strtolower('aBc'))  ≡ 'ABC'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($value) : mixed
 */
function pipeline(callable $a, callable $b)
{
    $list = func_get_args();

    return function ($value = null) use (&$list) {
        return array_reduce($list, function ($accumulator, callable $a) {
            return call_user_func($a, $accumulator);
        }, $value);
    };
}

const reverse = 'Functional\reverse';

/**
 * Call $function with arguments in reversed order
 *
 * @return \Closure
 * @param callable $function
 */
function reverse(callable $function)
{
    return function () use ($function) {
        return call_user_func_array($function, array_reverse(func_get_args()));
    };
}

const map = 'Functional\map';

/**
 * map :: Functor f => (a -> b) -> f a -> f b
 *
 * @return mixed|\Closure
 * @param callable $transformation
 * @param FantasyLand\FunctorInterface $value
 */
function map(callable $transformation = null, FantasyLand\FunctorInterface $value = null)
{
    return call_user_func_array(curryN(2, function (callable $transformation, FantasyLand\FunctorInterface $value) {
        return $value->map($transformation);
    }), func_get_args());
}

const bind = 'Functional\bind';

/**
 * bind :: Monad m => (a -> m b) -> m a -> m b
 *
 * @return mixed|\Closure
 * @param callable $function
 * @param FantasyLand\MonadInterface $value
 */
function bind(callable $function = null, FantasyLand\MonadInterface $value = null)
{
    return call_user_func_array(curryN(2, function (callable $function, FantasyLand\MonadInterface $value) {
        return $value->bind($function);
    }), func_get_args());
}

const join = 'Functional\join';

/**
 * join :: Monad (Monad m) -> Monad m
 *
 * @return FantasyLand\MonadInterface
 */
function join(FantasyLand\MonadInterface $monad = null)
{
    return $monad->bind(identity);
}

const mpipeline = 'Functional\mpipeline';

/**
 * mpipeline :: Monad m => (a -> m b) -> (b -> m c) -> m a -> m c
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($mValue) : mixed
 */
function mpipeline(callable $a, callable $b)
{
    return call_user_func_array(
        pipeline,
        array_map(bind, func_get_args())
    );
}

const mcompose = 'Functional\mcompose';

/**
 * compose :: Monad m => (b -> m c) -> (a -> m b) -> m a -> m c
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($mValue) : mixed
 */
function mcompose(callable $a, callable $b)
{
    return call_user_func_array(
        reverse(mpipeline),
        func_get_args()
    );
}

const flip = 'Functional\flip';

/**
 * flip :: (a -> b -> c) -> (b -> a -> c)
 *
 * @param callable $func
 * @return callable
 */
function flip(callable $func, $b = null, $a = null)
{
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array(curryN(2, function ($a, $b) use ($func) {
        $args = func_get_args();
        $args[0] = $b;
        $args[1] = $a;

        return call_user_func_array($func, $args);
    }), $args);
}

const isTraversable = 'Functional\isTraversable';

/**
 * Evaluate if value is a traversable
 *
 * @param mixed $value
 * @return bool
 */
function isTraversable($value)
{
    return is_array($value) || $value instanceof \Traversable;
}

const head = 'Functional\head';

/**
 * Return head of a traversable
 *
 * @param array|\Traversable $list
 * @return null|array|\Traversable
 */
function head($list)
{
    if (!isTraversable($list)) {
        return null;
    }
    foreach ($list as $item) {
        return $item;
    }
}

const tail = 'Functional\tail';

/**
 * Return tail of a traversable
 *
 * TODO support \Traversable
 *
 * @param array $list
 * @return null
 */
function tail(array $list)
{
    if (count($list) === 0) {
        return null;
    }

    $clone = $list;
    array_shift($clone);
    return $clone;
}

/**
 * tryCatch :: Exception e => (a -> b) -> (e -> b) -> a -> b
 *
 * @param callable $function
 * @param callable $catchFunction
 * @param $value
 * @return mixed
 */
function tryCatch(callable $function, callable $catchFunction, $value)
{
    return call_user_func_array(curryN(3, function (callable $function, callable $catchFunction, $value) {
        try {
            return call_user_func($function, $value);
        } catch (\Exception $e) {
            return call_user_func($catchFunction, $e);
        }
    }), func_get_args());
}

const reThrow = 'Functional\reThrow';

/**
 * reThrow :: Exception e => e -> a
 *
 * @param \Exception $e
 * @throws \Exception
 */
function reThrow(\Exception $e)
{
    throw $e;
}

/**
 * Lift result of transformation function , called with values from two monads.
 *
 * liftM2 :: Monad m => (a1 -> a2 -> r) -> m a1 -> m a2 -> m r
 *
 * Promote a function to a monad, scanning the monadic arguments from left to right. For example,
 *  liftM2 (+) [0,1] [0,2] = [0,2,1,3]
 *  liftM2 (+) (Just 1) Nothing = Nothing
 *
 * @param FantasyLand\MonadInterface $m1
 * @param FantasyLand\MonadInterface $m2
 * @param callable $transformation
 * @return FantasyLand\MonadInterface
 */
function liftM2(FantasyLand\MonadInterface $m1, FantasyLand\MonadInterface $m2, callable $transformation)
{
    return $m1->bind(function ($a) use ($m2, $transformation) {
        return $m2->bind(function ($b) use ($a, $transformation) {
            return call_user_func($transformation, $a, $b);
        });
    });
}

/**
 * liftA2 :: Applicative f => (a -> b -> c) -> f a -> f b -> f c
 *
 * @param FantasyLand\ApplicativeInterface $a1
 * @param FantasyLand\ApplicativeInterface $a2
 * @param callable $transformation
 * @return FantasyLand\ApplicativeInterface
 */
function liftA2(FantasyLand\ApplicativeInterface $a1, FantasyLand\ApplicativeInterface $a2, callable $transformation)
{
    return $a1->map(function ($a) use ($transformation) {
        return function ($b) use ($a, $transformation) {
            return call_user_func($transformation, $a, $b);
        };
    })->ap($a2);
}
