<?php
use Codeception\Util\Locator;

class BookCollectionCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // Test to verify that the booklist is displayed as expected
    public function showBookListTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');
        
        // Book list content
        $I->seeInTitle('Book Collection');
        $I->seeNumberOfElements('table#bookList>tbody>tr', 3);
        // Check sample book values
        $I->see('Jungle Book', 'tr#book1>td:nth-child(2)');
        $I->see('J. Walker', 'tr#book2>td:nth-child(3)');
        $I->see('Some smart gal.', 'tr#book3>td:nth-child(4)');
        $I->seeElement('tr#book1>td:first-child>a', ['href' => 'index.php?id=1']);
        $I->seeElement('tr#book2>td:first-child>a', ['href' => 'index.php?id=2']);
        $I->seeElement('tr#book3>td:first-child>a', ['href' => 'index.php?id=3']);
        
        // Add new book form content
        $I->seeElement('form#addForm>input', ['name' => 'title']);
        $I->seeElement('form#addForm>input', ['name' => 'author']);
        $I->seeElement('form#addForm>input', ['name' => 'description']);
        $I->seeElement('form#addForm>input', ['type' => 'submit',
                                              'value' => 'Add new book']);
    }
    
    // Test to verify that the book details page is displayed as expected
    public function showBookDetailsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');
        $I->click(1);
        $this->verifyBookDetails($I, 'Jungle Book', 'R. Kipling', 'A classic book.');
        $I->seeLink('Back to book list','index');
        
        // Buttons for updating and deleting book information
        $I->seeElement('form#modForm>input', ['type' => 'submit',
                                              'value' => 'Update book record']);
        $I->seeElement('form#delForm>input', ['type' => 'submit',
                                              'value' => 'Delete book record']);        
    }
    
    // Test to verify that non-numeric book id's are rejected when requesting book information
    public function invalidBookIdRejectedTest(AcceptanceTester $I)
    {
        $I->amOnPage("index.php?id=1'; drop table book;--");
        $I->seeInTitle('Error Page');        
    }	
    
    // Helper function that verifies that the book information on the current page matches the parameter values
    protected function verifyBookDetails(AcceptanceTester $I, String $title, String $author, String $description)
    {
        $I->seeInTitle('Book Details');
        $I->seeElement('form#modForm>input', ['name' => 'title',
                                              'value' => $title]);
        $I->seeElement('form#modForm>input', ['name' => 'author',
                                              'value' => $author]);
        $I->seeElement('form#modForm>input', ['name' => 'description',
                                              'value' => $description]);
    }
   
    // Test to verify that new books can be added. Four cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description"
    //   2. title=>"New book", author=>"Some author", description=>""
    //   3. title=>"A Girl's memoirs", author=>"Jean d'Arc", description=>"Single quotes (') should not break anything"
    //   4. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function successfulAddBookTest(AcceptanceTester $I)
    {
        $testValues = ['title' => "New book",
                       'author' => "Some author",
                       'description' => "Some description"];
		for($nmb = 0; $nmb < 4; $nmb++){
			if($nmb == 1){
				$testValues = ['title' => "New book",
                       'author' => "Some author",
                       'description' => ""];
			} elseif($nmb == 2){
				$testValues = ['title' => "A Girl's memoirs",
                       'author' => "Jean d'Arc",
                       'description' => "Single quotes (') should not break anything"];
			} elseif($nmb == 3){
				$testValues = ['title' => "<script>document.body.style.visibility='hidden'</script>",
                       'author' => "<script>document.body.style.visibility='hidden'</script>",
                       'description' => "<script>document.body.style.visibility='hidden'</script>"];
			}
		
        $I->amOnPage('index.php');
        $I->submitForm('#addForm', ['title' => $testValues['title'], 
                                    'author' => $testValues['author'],
                                    'description' => $testValues['description']]);

        // Getting booklist with new book added as ID:4
        $I->seeInTitle('Book Collection');
        $I->seeNumberOfElements('table#bookList>tbody>tr', 4+$nmb);
		$tot = 4+$nmb;
        $I->see('ID: ' . $tot);
        $I->seeElement('tr#book' . $tot .'>td:first-child>a', ['href' => 'index.php?id=' . $tot]);
        $I->see($testValues['title'], 'tr#book' . $tot . '>td:nth-child(2)');
        $I->see($testValues['author'], 'tr#book' . $tot . '>td:nth-child(3)');
        $I->see($testValues['description'], 'tr#book' . $tot . '>td:nth-child(4)');
        $I->seeLink($tot,'index.php?id=' . $tot);
		}
		
		
   }
    
    // Test to verify that adding a book fails if mandatory fields are missing
    public function addBookWithoutMandatoryFieldsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php'); 
		$I->submitForm('#addForm', ['title' => '', 
                                    'author' => '',
                                    'description' => '']);
		$I->see('Error Page');
		
    }
    
    // Test to verify that book records can be modified successfully. Four cases should be verified:
    //   1. title=>"Different title", author=>"Different Author", description=>"Different description"
    //   2. title=>"Different title", author=>"Different Author", description=>""
    //   3. title=>"A Girl's memoirs", author=>"Jean d'Arc", description=>"Single quotes (') should not break anything"
    //   4. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function successfulModifyBookTest(AcceptanceTester $I)
    {	
	$testValues = ['title' => "Different title",
                    'author' => "Different author",
                    'description' => "Different description"];
	//first
	for($nmb = 0; $nmb < 4; $nmb++){
		if($nmb==1){
			$testValues['description'] = "";
		} elseif($nmb==2){
			$testValues['title'] = "A Girl's memoirs";
			$testValues['author'] = "Jean d'Arc";
			$testValues['description'] = "Single quotes (') should not break anything";
		} elseif($nmb==3){
			$testValues['title'] = "<script>document.body.style.visibility='hidden'</script>";
			$testValues['author'] = "<script>document.body.style.visibility='hidden'</script>";
			$testValues['description'] = "<script>document.body.style.visibility='hidden'</script>";
		}
		
        $I->amOnPage('index.php?id=2');        
		$I->fillfield('title', $testValues['title']);
		$I->fillfield('author', $testValues['author']);
		$I->fillfield('description', $testValues['description']);
		$I->click('Update book record');
		
		$I->see($testValues['title'], 'tr#book2>td:nth-child(2)');
        $I->see($testValues['author'], 'tr#book2>td:nth-child(3)');
        $I->see($testValues['description'], 'tr#book2>td:nth-child(4)');
        $I->seeLink('2','index.php?id=2');
	}	  
    }
    
    // Test to verify that modifying a book fails if mandatory fields are missing
    public function modifyBookWithoutMandatoryFieldsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php?id=2');         
		$I->fillfield('title', '');
		$I->fillfield('author', '');
		$I->fillfield('description', 'A description');
		$I->click('Update book record');		
		$I->see('Error Page');
    }
    
    // Test to verify that deleting a book succeeds.
    public function successfulDeleteBookTest(AcceptanceTester $I)
    {
		 $I->amOnPage('index.php');
		 $I->see('Moonwalker');
		 $I->click('2'); 
		 $I->click('Delete book record');
		 $I->dontsee('Moonwalker');
    }
    
    // Test to verify that deleting a book succeeds.
    public function deleteBookWithInvalidIdTest(AcceptanceTester $I)
    {
         $I->amOnPage('index.php?id=2'); 
		
		 $I->submitForm('#delForm', ['op' => 'del',
									 'id' => 'a']);
		 
		 $I->see('Moonwalker');
    }
}