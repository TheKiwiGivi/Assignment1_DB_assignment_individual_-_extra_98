<?php
/** The abstract class constituting the top of the Model of the IMT2571 Assignment #1 MVC-example.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */

namespace NO\NTNU\IMT2571\Assignment1\Model;

use InvalidArgumentException;
// Included because of the documentation
use Exception;

/** The Model classes holdng data about a collection of books. This abstract class
 * offers methods for validating model data.
 */
abstract class AbstractModel
{
   
    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
     * @throws Exception
     */
    abstract public function getBookList();
    
    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
     * @throws Exception
     */
    abstract public function getBookById($id);
    
    /** Adds a new book to the collection.
     * @param $book Book The book to be added - the id of the book will be set after
     *                   successful insertion.
     * @throws Exception
     */
    abstract public function addBook($book);

    /** Modifies data related to a book in the collection.
     * @param $book Book The book data to be kept.
     * @throws Exception
     */
    abstract public function modifyBook($book);

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     * @throws Exception
    */
    abstract public function deleteBook($id);

    /** Helper function verifying that ids are numbers.
     * @param integer $id The book id to check.
     * @throws InvalidArgumentException if $id is not a number.
     * @static
     */
    public static function verifyId($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("Book id expected to a valid number");
        }
    }
    
    /** Helper function verifying that book data is valid - i.e., that $id is a valid
     *  number (only when $ignoreId is true) and that title and author are non-empty strings.
     * @param Book $book The book record to check.
     * @param boolean $ignoreId The id of book is only checked if $ignoreId is false.
     * @throws InvalidArgumentException if book data is invalid.
     * @static
     */
    public static function verifyBook($book, $ignoreId = false)
    {
        if (!$ignoreId && !is_numeric($book->id)) {
            throw new InvalidArgumentException("Book id expected to a valid number");
        }
        if ($book->title == null || $book->title == '') {
            throw new InvalidArgumentException("Book title is mandatory");
        }
        if ($book->author == null || $book->author == '') {
            throw new InvalidArgumentException("Book author is mandatory");
        }
    }
}
