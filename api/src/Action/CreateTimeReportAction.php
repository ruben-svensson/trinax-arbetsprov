<?php declare(strict_types=1);
namespace App\Action;

use App\Api\TrinaxApiServiceInterface;
use App\Database;
use App\Dto\CreateTimeReportDTO;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class CreateTimeReportAction extends Action {

    public function __construct(
        private TrinaxApiServiceInterface $service,
        private Database $database
    )
    {}

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            // Get form data (text fields)
            $postData = $request->getParsedBody();
            
            // Validate and create the DTO
            $createDto = CreateTimeReportDTO::fromArray($postData);

            // Create the time report via the service
            $newReport = $this->service->createTimeReport($createDto);

            // Handle the uploaded image
            $uploadedFiles = $request->getUploadedFiles();
            $imageName = 'no_image'; // Default if no image uploaded
            
            if (isset($uploadedFiles['image'])) {
                $imageName = $this->handleImageUpload($uploadedFiles['image']);
            }

            // Link the image to the report
            $this->database->linkImageToReport($newReport->id, $imageName);

            return $this->jsonResponse($response, $newReport, 201);
            
        } catch (InvalidArgumentException $e) {
            return $this->jsonResponse($response, ['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->jsonResponse($response, ['error' => 'An unexpected error occurred'], 500);
        }
    }

    private function handleImageUpload(UploadedFileInterface $uploadedFile): string {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('File upload failed');
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($uploadedFile->getClientMediaType(), $allowedTypes)) {
            throw new InvalidArgumentException('Invalid image type. Allowed types: JPEG, PNG, JPG, WEBP');
        }

        $fiveMB = 5 * 1024 * 1024;
        if ($uploadedFile->getSize() > $fiveMB) {
            throw new InvalidArgumentException('Image must be smaller than 5MB');
        }

        // Generate a unique filename
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        // Move the file to the uploads directory
        $uploadedFile->moveTo('/app/uploads/' . $filename);

        return $filename;
    }
}