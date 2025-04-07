<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Mockery;
use App\Services\API\V1\Service\ContactService;
use App\Repositories\API\V1\Contracts\ContactRepositoryInterface;
use App\Models\API\V1\Contact;

class ContactServiceUnitTest extends TestCase
{
    protected $contactService;
    protected $contactRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the ContactRepositoryInterface
        $this->contactRepository = Mockery::mock(ContactRepositoryInterface::class);

        // Inject the mocked repository into ContactService
        $this->contactService = new ContactService($this->contactRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_contact_can_be_retrieved()
    {
        $contactId = 1;
        $mockContact = new Contact([
            'id' => $contactId,
            'contact_fullname' => 'Test Name',
            'contact_email' => 'test@example.com',
        ]);

        // Mock the behavior of the repository's `find` method
        $this->contactRepository->shouldReceive('find')
            ->once()
            ->with($contactId)
            ->andReturn($mockContact);

        $result = $this->contactService->getContactById($contactId);

        $this->assertInstanceOf(Contact::class, $result);
        $this->assertEquals($contactId, $result->id);
    }

    public function test_contact_can_be_created()
    {
        $contactData = [
            'contact_fullname' => 'New Contact',
            'contact_email' => 'new@example.com',
        ];

        $mockContact = new Contact($contactData);

        // Mock the repository's `create` method
        $this->contactRepository->shouldReceive('create')
            ->once()
            ->with($contactData)
            ->andReturn($mockContact);

        $result = $this->contactService->saveContact($contactData);

        $this->assertInstanceOf(Contact::class, $result);
        $this->assertEquals('New Contact', $result->contact_fullname);
    }

    public function test_contact_can_be_updated()
    {
        $contactId = 1;
        $updatedData = [
            'contact_fullname' => 'Updated Name',
            'contact_email' => 'updated@example.com',
        ];

        // Mock the repository's `update` method
        $this->contactRepository->shouldReceive('update')
            ->once()
            ->with($contactId, $updatedData)
            ->andReturn(true);

        $result = $this->contactService->updateContact($contactId, $updatedData);

        $this->assertTrue($result);
    }

    public function test_contact_can_be_deleted()
    {
        $contactId = 1;

        // Mock the repository's `delete` method
        $this->contactRepository->shouldReceive('delete')
            ->once()
            ->with($contactId)
            ->andReturn(true);

        $result = $this->contactService->deleteContact($contactId);

        $this->assertTrue($result);
    }
}
