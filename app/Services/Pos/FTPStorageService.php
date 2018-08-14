<?php


namespace App\Services\Pos;


use App\Utilities\HashTools;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use function is_null;

class FTPStorageService
{

    /** @var  ShopService */
    private $shopService;
    private $fileExtension;
    private $fileName;
    const FOLDER       = "chu_c_test_file/";
    const DOWNLOAD_URL = "http://lovegreenfood.com/" . self::FOLDER;
    const UPLOAD_URL   = "public_html/" . self::FOLDER;


    /**
     * @param mixed $shopService
     * @return FTPStorageService
     */
    public function setShopService($shopService)
    {
        $this->shopService = $shopService;
        return $this;
    }


    /**
     * @param mixed $ext
     * @return FTPStorageService
     */
    public function setFileExtension($ext)
    {
        $this->fileExtension = $ext;
        return $this;
    }


    /**
     * @param $image
     * @return string
     * @throws Exception
     */
    public function storeFileAs($image)
    {
        if (is_null($this->shopService)) {
            throw new Exception('ShopService is not found');
        }

        $fileName = $this->generateFileName(ShopService::PRODUCT);

        Storage::disk('ftp')
               ->putFileAs(
                   $this->getUploadUrl(ShopService::PRODUCT),
                   $image,
                   $fileName
               );

        return $this->getDownloadUrl(ShopService::PRODUCT);
    }


    public function deleteImageFile($fileName, $category)
    {
        return Storage::disk('ftp')->delete($this->getUploadUrl($category) . $fileName);
    }


    public function generateFileName($category)
    {
        if ($this->validCategory($category)) {
            $hash           = HashTools::generateHash();
            $this->fileName = "{$category}_{$hash}.{$this->fileExtension}";
            return $this->fileName;
        };
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFileName()
    {
        if (!isset($this->fileName)) {
            throw new \Exception("Not Generate A File Name");
        }
        return $this->fileName;

    }


    /**
     * @param mixed $fileName
     * @return FTPStorageService
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }


    private function validCategory($category)
    {
        if (!in_array($category, ShopService::CATEGORY)) {
            throw new Exception('Invalid Category :' . $category);
        }
        return true;
    }


    public function getDownloadUrl($category)
    {
        $shopSn = $this->shopService->getShopSn();
        return self::DOWNLOAD_URL . "{$shopSn}/{$category}/" . $this->fileName;
    }


    public function getUploadUrl($category)
    {
        $shopSn = $this->shopService->getShopSn();
        return self::UPLOAD_URL . "{$shopSn}/{$category}/";
    }


}