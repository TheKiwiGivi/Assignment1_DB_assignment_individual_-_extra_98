<?php

require_once('Model/DBModel.php');
require_once("Model/Book.php");

use NO\NTNU\IMT2571\Assignment1\Model\DBModel;
use NO\NTNU\IMT2571\Assignment1\Model\Book;

class BookCollectionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $dbModel;
    
    protected function _before()
    {
        $db = new PDO(
                'mysql:host=localhost;dbname=test;charset=utf8',
                'root',
                '',
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        $this->dbModel = new DBModel($db);
    }

    protected function _after()
    {
    }

    // Test that all books are retrieved from the database
    public function testGetBookList()
    {
        $bookList = $this->dbModel->getBookList();

        // Sample tests of book list contents
        $this->assertEquals(count($bookList), 3);
        $this->assertEquals($bookList[0]->id, 1);
        $this->assertEquals($bookList[0]->title, 'Jungle Book');
        $this->assertEquals($bookList[1]->id, 2);
        $this->assertEquals($bookList[1]->author, 'J. Walker');
        $this->assertEquals($bookList[2]->id, 3);
        $this->assertEquals($bookList[2]->description, 'Written by some smart gal.');
    }

    // Tests that information about a single book is retrieved from the database
    public function testGetBook()
    {
        $book = $this->dbModel->getBookById(1);

        // Sample tests of book list contents
        $this->assertEquals($book->id, 1);
        $this->assertEquals($book->title, 'Jungle Book');
        $this->assertEquals($book->author, 'R. Kipling');
        $this->assertEquals($book->description, 'A classic book.');
    }

    // Tests that get book operation fails if id is not numeric
    public function testGetBookRejected()
    {
       $book;
		try {
		$book = $this->dbModel->getBookById('a');
		}
		catch(Exception $e){
			//should run this
			
		}
		$this->assertNull($book);
		
    }

    // Tests that a book can be successfully added and that the id was assigned. Four cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description" 
    //   2. title=>"New book", author=>"Some author", description=>""
    //   3. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function testAddBook()
    {
        //add book
		$bookInfo = ['title' => 'New book', 
					 'author' => 'Some author', 
					 'description' => 'Some description'];
					 
		for($nmb = 0; $nmb < 1; $nmb++){
			if($nmb == 1){
			$bookInfo = ['title' => 'New book', 'author' => 'Some author','description' => ''];
			} elseif($nmb == 2){
			$bookInfo = ['title' => "<script>document.body.style.visibility='hidden'</script>", 
					 'author' => "<script>document.body.style.visibility='hidden'</script>", 
			'description' => "<script>document.body.style.visibility='hidden'</script>"];
			}
		$book = new Book($bookInfo['title'], $bookInfo['author'], $bookInfo['description']);
		
		
		$this->dbModel->addBook($book);
		$book = $this->dbModel->getBookById(4+$nmb);
		
		$this->assertEquals($book->id, 4+$nmb);
        $this->assertEquals($book->title, $bookInfo['title']);
        $this->assertEquals($book->author, $bookInfo['author']);
        $this->assertEquals($book->description, $bookInfo['description']);
		}
    }

    // Tests that adding a book fails if id is not numeric
    public function testAddBookRejectedOnInvalidId()
    {
		$bookInfo = ['title' => 'A book', 
					 'author' => 'An author', 
					 'description' => 'A description',
					 'id' => 'a'];
					 try {
		$book = new Book($bookInfo['title'], $bookInfo['author'], $bookInfo['description'], $bookInfo['id']);
		$this->dbModel->addBook($book);
					 }
					 catch(Exception $e){
						 
					 }
		
		$bookList = $this->dbModel->getBookList();
		$this->assertEquals(count($bookList), 3);
		
    }

    // Tests that adding a book fails mandatory fields are left blank
    public function testAddBookRejectedOnMandatoryFieldsMissing()
    {
				$bookInfo = ['title' => '', 
					 'author' => '', 
					 'description' => 'A description'];
		try {
		$this->dbModel->addBook($bookInfo);							  
		}
		catch(Exception $e){
			
		}
       $bookList = $this->dbModel->getBookList();
        $this->assertEquals(count($bookList), 3);
    }

    // Tests that a book record can be successfully modified. Three cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description"
    //   2. title=>"New book", author=>"Some author", description=>""
    //   3. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function testModifyBook()
    {
		$bookInfo = ['title' => 'New book', 
					 'author' => 'Some author', 
					 'description' => 'Some description',
					 'id' => '1'];
		for($nmb = 0; $nmb < 1; $nmb++){
			if($nmb == 1){
			$bookInfo = ['title' => 'New book', 'author' => 'Some author','description' => ''];
			} elseif($nmb == 2){
			$bookInfo = ['title' => "<script>document.body.style.visibility='hidden'</script>", 
					 'author' => "<script>document.body.style.visibility='hidden'</script>", 
			'description' => "<script>document.body.style.visibility='hidden'</script>"];
			}
		$book = new Book($bookInfo['title'], $bookInfo['author'], $bookInfo['description'], $bookInfo['id']);
		$this->dbModel->modifyBook($book);
		//$book = $this->dbModel->getBookById($bookInfo['id']);
		
		$this->assertEquals($book->id, $bookInfo['id']);
        $this->assertEquals($book->title, $bookInfo['title']);
        $this->assertEquals($book->author, $bookInfo['author']);
        $this->assertEquals($book->description, $bookInfo['description']);
		}
		
    }
    
    // Tests that modifying a book record fails if id is not numeric
    public function testModifyBookRejectedOnInvalidId()
    {       
	$bookInfo = ['title' => 'New book', 
				 'author' => 'Some author', 
				 'description' => 'Some description',
				 'id' => 'a'];
				 $book = new Book($bookInfo['title'], $bookInfo['author'], $bookInfo['description'], $bookInfo['id']);
				 $this->dbModel->modifyBook($book);
				 
				 $bookList = $this->dbModel->getBookList();
				 $this->assertEquals($bookList[0]->id, '1');
    }
    
    // Tests that modifying a book record fails if mandatory fields are left blank
    public function testModifyBookRejectedOnMandatoryFieldsMissing()
    {       
	$bookInfo = ['title' => '', 
				 'author' => '', 
				 'description' => 'Some description'];
				 try {
				 $this->dbModel->modifyBook($bookInfo, 1);
				 
				 }
				 catch(Exception $e){
					 
				 }
				 $bookList = $this->dbModel->getBookList();
				 $this->assertEquals($bookList[0]->title, 'Jungle Book');
    }
    
    // Tests that a book record can be successfully modified.
    public function testDeleteBook()
    {
		$this->dbModel->deleteBook('1');
		
		$bookList = $this->dbModel->getBookList();
        $this->assertEquals(count($bookList), 2);
		
    }
    
    // Tests that adding a book fails if id is not numeric
    public function testDeleteBookRejectedOnInvalidId()
    {
		$this->dbModel->deleteBook('a');
		
		$bookList = $this->dbModel->getBookList();
        $this->assertEquals(count($bookList), 3);
    }
}