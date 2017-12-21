<?php

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use Widmogrod\FantasyLand;
use Widmogrod\Functional as f;

class ListtNil implements Listt
{
    use Common\PointedTrait;

    public const of = 'Widmogrod\Primitive\Listt::of';

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * fs <*> xs = [f x | f <- fs, x <- xs]
     */
    public function ap(FantasyLand\Apply $applicative)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        return $accumulator;
    }

    /**
     * @inheritdoc
     *
     * Example from haskell source code:
     * ``` haskell
     * traverse f = List.foldr cons_f (pure [])
     *  where cons_f x ys = (:) <$> f x <*> ys
     * ```
     *
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     * (<*>) :: f (a -> b) -> f a -> f b
     */
    public function traverse(callable $f)
    {
        throw new EmptyListError(__FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of([]);
    }

    /**
     * @inheritdoc
     */
    public function getEmpty()
    {
        return self::mempty();
    }

    /**
     * @inheritdoc
     *
     * @throws TypeMismatchError
     */
    public function concat(FantasyLand\Semigroup $value)
    {
        if ($value instanceof Listt) {
            return $value;
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @inheritdoc
     */
    public function equals($other)
    {
        return $other instanceof self
            ? true
            : false;
    }

    /**
     * @inheritdoc
     */
    public function head()
    {
        throw new EmptyListError(__FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function tail(): Listt
    {
        throw new EmptyListError(__FUNCTION__);
    }
}