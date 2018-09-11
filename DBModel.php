<?php	
/** The Model implementation of the IMT2571 Assignment #1 MVC-example, storing data in a MySQL database using PDO.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */

namespace NO\NTNU\IMT2571\Assignment1\Model;

use PDO;
// Included for the documentation
use PDOException;

require_once("AbstractModel.php");
require_once("Book.php");

/** The Model is the class holding data about a collection of books.
 * @todo implement class functionality.
 */
class DBModel extends AbstractModel
{
    protected $db = null;
    
    /**
     * @param PDO $db PDO object for the database; a new one will be created if no PDO object
     *                is passed
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function __construct($db = null)
    {
		
        if ($db) {
            $this->db = $db;
			
        } else {
            // Create PDO connection
			try {
			$this->db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			}
			catch (PDOException $e){
				echo "Something somewhere went completely wrong.";
			}
        }
    }
    
    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function getBookList()
    {
        $booklist = array();
		
	$stmt = $this->db->prepare("SELECT * FROM book");
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($results as $row){
		$booklist[] = new book($row['title'], $row['author'], $row['description'], $row['id']);
	}
        return $booklist;
    }
    
    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function getBookById($id)
    {
		//should probably have used self::verifyId here
		if(is_numeric($id)){
		try {
        self::verifyId($id);
		$stmt = $this->db->prepare("SELECT * FROM book WHERE id=:id");
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
        $book = new book($result['title'], $result['author'], $result['description'], $result['id']);
		self::verifyBook($book);
		
        return $book;
		}
		
		catch (Exception $e) {}
		}
		
		//should maybe return something else here
    }
    
    /** Adds a new book to the collection.
     * @param Book $book The book to be added - the id of the book will be set after successful insertion.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function addBook($book)
    {		
	//should probably have used self::verifyId here
	if(is_numeric($book->id)){
		try {
	self::verifyBook($book, true);
	$stmt = $this->db->prepare("INSERT INTO book(title, author, description) VALUES (?, ?, ?)");
	$stmt->execute(array($book->title, $book->author, $book->description));
	$book->id = $this->db->lastInsertId();
		}
		catch(Exception $e) {}
		}
	}
		

    /** Modifies data related to a book in the collection.
     * @param Book $book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
    */
    public function modifyBook($book)
    {
		//should probably have used self::verifyId here as well
		if(is_numeric($book->id)){
			try {
        self::verifyBook($book);
		
		$stmt = $this->db->prepare("UPDATE book SET title = ?, author = ?, description = ? WHERE id = ?");
		$stmt->execute(array($book->title, $book->author, $book->description, $book->id));
			}
			catch(Exception $e){
				echo "Something went wroong.";
			}
		}
		
    }

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
    */
    public function deleteBook($id)
    {
		if(is_numeric($id)){
		try {
        self::verifyId($id);
		$stmt = $this->db->prepare("DELETE FROM book WHERE id = ?");
		$stmt->execute(array($id));
		}
		catch(Exception $e){
			
		}
		
    }
	}
}
