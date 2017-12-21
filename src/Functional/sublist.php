<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;

/**
 * take :: Int -> [a] -> [a]
 *
 * take n, applied to a list xs, returns the prefix of xs of length n, or xs itself if n > length xs:
 *
 * @param int $n
 * @param Listt $xs
 * @return Listt
 */
function take(int $n, Listt $xs = null)
{
    return curryN(2, function (int $n, Listt $xs): Listt {
        if ($n < 1) {
            return fromNil();
        }

        if ($n > length($xs)) {
            return $xs;
        }

        try {
            return prepend(head($xs), take($n - 1, tail($xs)));
        } catch (EmptyListError $e) {
            return fromNil();
        }
    })(...func_get_args());
}

/**
 * drop :: Int -> [a] -> [a]
 *
 * drop n xs returns the suffix of xs after the first n elements, or [] if n > length xs:
 * @param int $n
 * @param Listt $xs
 * @return Listt
 */
function drop(int $n, Listt $xs = null)
{
    return curryN(2, function (int $n, Listt $xs): Listt {
        if ($n < 1) {
            return $xs;
        }

        if ($n > length($xs)) {
            return fromNil();
        }

        try {
            return drop($n - 1, tail($xs));
        } catch (EmptyListError $e) {
            return fromNil();
        }
    })(...func_get_args());
}

/**
 * splitAt :: Int -> [a] -> ([a], [a])
 *
 * splitAt n xs returns a tuple where first element is xs prefix of length n and second element is the remainder of the list:
 */
function splitAt()
{
    // TODO
}

/**
 * takeWhile :: (a -> Bool) -> [a] -> [a]
 *
 * takeWhile, applied to a predicate p and a list xs, returns the longest prefix (possibly empty) of xs of elements that satisfy p:
 */
function takeWhile()
{
    // TODO
}

/**
 * dropWhile :: (a -> Bool) -> [a] -> [a]
 *
 * dropWhile p xs returns the suffix remaining after takeWhile p xs:
 */
function dropWhile()
{
    // TODO
}

/**
 * span :: (a -> Bool) -> [a] -> ([a], [a])
 *
 * span, applied to a predicate p and a list xs, returns a tuple where first element is longest prefix (possibly empty) of xs of elements that satisfy p and second element is the remainder of the list:
 */
function span()
{
    // TODO
}

/**
 * break :: (a -> Bool) -> [a] -> ([a], [a])
 *
 * break, applied to a predicate p and a list xs, returns a tuple where first element is longest prefix (possibly empty) of xs of elements that do not satisfy p and second element is the remainder of the list:
 */
function breakk()
{
    // TODO
}