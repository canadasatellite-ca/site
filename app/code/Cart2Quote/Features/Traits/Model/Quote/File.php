<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote;
use \Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
/**
 * Trait File
 * @package Cart2Quote\Quotation\Model\Quote
 */
trait File
{
    /**
     * @param $fileAmount
     * @return array
     * @throws \Exception
     */
    private function uploadFiles($fileAmount)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$imagesData = [];
        $allowedExtensions = $this->fileUploadHelper->getAllowedFileExtensions();
        $usedFileTitles = [];
        for ($i = 0; $i < $fileAmount; $i++) {
            $fileTitle = null;
            $fileTitlePost = $this->request->getPost('title_' . $i);
            if (!empty($fileTitlePost)) {
                $fileTitle = $fileTitlePost;
            }
            $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'fileupload_' . $i]);
            $uploaderFactory->setAllowedExtensions($allowedExtensions);
            $uploaderFactory->setAllowRenameFiles(false);
            $uploaderFactory->setFilesDispersion(false);
            if (!empty($fileTitle)) {
                $fileExtention = $uploaderFactory->getFileExtension();
                $orgFileTitle = $fileTitle;
                $existingData = $this->getFileDataFromSession();
                if (!is_array($existingData)) {
                    $existingData = [];
                }
                $counter = 1;
                while (in_array(strtolower($fileTitle), $usedFileTitles)
                    || array_key_exists($fileTitle . '.' . $fileExtention, $existingData)) {
                    $fileTitle = $orgFileTitle . '_' . $counter;
                    $counter++;
                }
                $usedFileTitles[] = strtolower($fileTitle);
                $fileTitle = $fileTitle . '.' . $fileExtention;
            }
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $destinationPath = $mediaDirectory->getAbsolutePath('quotation') . DIRECTORY_SEPARATOR . 'temp';
            $imageData = $uploaderFactory->save($destinationPath, $fileTitle);
            $fileTitle = null;
            $this->setImageDataToSession($imageData);
            $imagesData[] = $imageData;
        }
        return $imagesData;
		}
	}
    /**
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function removeFile($fileName)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('quotation');
        $path .= DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
        try {
            //delete file
            $this->fileDriver->deleteFile($path . $fileName);
            //update session list of files
            $existingData = $this->getFileDataFromSession();
            unset($existingData[$fileName]);
            $this->quoteSession->setUploadedFile($existingData);
        } catch (\Exception $exception) {
            throw new FileSystemException(
                new \Magento\Framework\Phrase(
                    'The "%1" file can\'t be deleted.',
                    [$fileName]
                )
            );
        }
		}
	}
    /**
     * @param array $imageData
     */
    private function setImageDataToSession($imageData)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$existingData = $this->getFileDataFromSession();
        if (is_array($existingData)) {
            $data = $existingData;
        }
        $data[$imageData['file']] = $imageData;
        $this->quoteSession->setUploadedFile($data);
		}
	}
    /**
     * @return array|null
     */
    private function getFileDataFromSession()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quoteSession->getUploadedFile();
		}
	}
    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function saveFileQuotationQuote($quotationQuote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteId = $quotationQuote->getId();
        $files = $this->getFileDataFromSession();
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('quotation/' . $quoteId . '/');
        $this->fileDriver->createDirectory($path);
        foreach ($files as $file) {
            $source = $file['path'] . '/' . $file['file'];
            if (strpos($source, '/quotation/') === false) {
                //don't allow files that arn't in the quotation folder
                continue;
            }
            $destination = $path . $file['file'];
            $this->ioFile->mv($source, $destination);
        }
		}
	}
    /**
     * @return array
     */
    private function getFileDataFromQuotation()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteId = $this->backendSessionQuote->getQuotationQuoteId();
        if ($quoteId) {
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            if ($mediaDirectory->isExist('quotation/' . $quoteId)) {
                return $mediaDirectory->read('quotation/' . $quoteId);
            }
        }
		}
	}
    /**
     * @param string $fileName
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     */
    private function downloadFile($fileName)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fileName = str_replace('..', '', $fileName);
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $newfileName = $mediaDirectory->getRelativePath($fileName);
        $path = $mediaDirectory->getAbsolutePath($fileName);
        if ($mediaDirectory->isFile($fileName)) {
            return $this->fileFactory->create(
                $newfileName,
                $this->ioFile->read($path)
            );
        } else {
            throw new \Exception((string)new \Magento\Framework\Phrase('File not found'));
        }
		}
	}
}
