<?php

namespace Weirin\Combinatorics;

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Math_Combinatorics
 *
 * Math_Combinatorics provides the ability to find all combinations and
 * permutations given an set and a subset size.  Associative arrays are
 * preserved.
 *
 * PHP version 5
 *
 * @category   Math
 * @package    Combinatorics
 * @author     David Sanders <shangxiao@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 1.0.0
 * @link       http://pyrus.sourceforge.net/Math_Combinatorics.html
 */


/**
 * Math_Combinatorics
 *
 * Math_Combinatorics provides the ability to find all combinations and
 * permutations given an set and a subset size.  Associative arrays are
 * preserved.
 *
 * @category   Math
 * @package    Combinatorics
 * @author     David Sanders <shangxiao@php.net>
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 1.0.0
 * @link       http://pyrus.sourceforge.net/Math_Combinatorics.html
 */

class Combinatorics
{
    /**
     * List of pointers that record the current combination.
     *
     * @var array
     * @access private
     */
    private $_pointers = [];

    /**
     * Find all combinations given a set and a subset size.
     *
     * @access public
     * @param  array $set          Parent set
     * @param  int   $subsetSize  Subset size
     * @return array An array of combinations
     */
    public function combinations(array $set, $subsetSize = null)
    {
        $set_size = count($set);

        if (is_null($subsetSize)) {
            $subsetSize = $set_size;
        }

        if ($subsetSize >= $set_size) {
            return [$set];
        } else if ($subsetSize == 1) {
            return array_chunk($set, 1);
        } else if ($subsetSize == 0) {
            return [];
        }

        $combinations = [];
        $setKeys = array_keys($set);
        $this->_pointers = array_slice(array_keys($setKeys), 0, $subsetSize);

        $combinations[] = $this->_getCombination($set);
        while ($this->_advancePointers($subsetSize - 1, $set_size - 1)) {
            $combinations[] = $this->_getCombination($set);
        }

        return $combinations;
    }

    /**
     * Recursive function used to advance the list of 'pointers' that record the
     * current combination.
     *
     * @access private
     * @param  int $pointerNumber The ID of the pointer that is being advanced
     * @param  int $limit          Pointer limit
     * @return bool True if a pointer was advanced, false otherwise
     */
    private function _advancePointers($pointerNumber, $limit)
    {
        if ($pointerNumber < 0) {
            return false;
        }

        if ($this->_pointers[$pointerNumber] < $limit) {
            $this->_pointers[$pointerNumber]++;
            return true;
        } else {
            if ($this->_advancePointers($pointerNumber - 1, $limit - 1)) {
                $this->_pointers[$pointerNumber] =
                    $this->_pointers[$pointerNumber - 1] + 1;
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the current combination.
     *
     * @access private
     * @param  array $set The parent set
     * @return array The current combination
     */
    private function _getCombination($set)
    {
        $setKeys = array_keys($set);

        $combination = [];

        foreach ($this->_pointers as $pointer) {
            $combination[$setKeys[$pointer]] = $set[$setKeys[$pointer]];
        }

        return $combination;
    }

    /**
     * Find all permutations given a set and a subset size.
     *
     * @access public
     * @param  array $set          Parent set
     * @param  int   $subsetSize  Subset size
     * @return array An array of permutations
     */
    public function permutations(array $set, $subsetSize = null)
    {
        $combinations = $this->combinations($set, $subsetSize);
        $permutations = [];

        foreach ($combinations as $combination) {
            $permutations = array_merge($permutations,
                                        $this->_findPermutations($combination));
        }

        return $permutations;
    }

    /**
     * Recursive function to find the permutations of the current combination.
     *
     * @access private
     * @param array $set Current combination set
     * @return array Permutations of the current combination
     */
    private function _findPermutations($set)
    {
        if (count($set) <= 1) {
            return [$set];
        }

        $permutations = [];

        list($key, $val) = $this->array_shift_assoc($set);
        $sub_permutations = $this->_findPermutations($set);

        foreach ($sub_permutations as $permutation) {
            $permutations[] = array_merge([$key => $val], $permutation);
        }

        $set[$key] = $val;

        $start_key = $key;

        $key = $this->_firstKey($set);
        while ($key != $start_key) {

            list($key, $val) = $this->array_shift_assoc($set);
            $sub_permutations = $this->_findPermutations($set);

            foreach ($sub_permutations as $permutation) {
                $permutations[] = array_merge([$key => $val], $permutation);
            }

            $set[$key] = $val;
            $key = $this->_firstKey($set);
        }

        return $permutations;
    }

    /**
     * Associative version of array_shift()
     *
     * @access public
     * @param  array $array Reference to the array to shift
     * @return array Array with 1st element as the shifted key and the 2nd
     *               element as the shifted value
     */
    public function array_shift_assoc(array &$array)
    {
        foreach ($array as $key => $val) {
            unset($array[$key]);
            break;
        }
        return [$key, $val];
    }

    /**
     * Get the first key of an associative array
     *
     * @param  array $array Array to find the first key
     * @access private
     * @return mixed The first key of the given array
     */
    private function _firstKey($array)
    {
        foreach ($array as $key => $val) {
            break;
        }
        return $key;
    }
}

