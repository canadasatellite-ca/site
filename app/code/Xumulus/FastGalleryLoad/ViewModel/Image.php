<?php

namespace Xumulus\FastGalleryLoad\ViewModel;

class Image implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public function __construct()
    {

    }

    public function getMainThumbHtml($imagesJson, $thumbwidth)
    {
        $imagesData = json_decode($imagesJson,true);
        $mainThumbHtml = '';
        if(count($imagesData)>0){
            foreach($imagesData as $key=>$value){
                if(isset($value['isMain']) && $value['isMain'] == 1){
                    $mainImage = $value['img'];
                    $mainMainImageTitle = $value['caption'];
                    $mainThumbHtml.='<div class="fotorama__nav__frame fotorama__nav__frame--thumb fotorama__active" tabindex="0" role="button" data-gallery-role="nav-frame" data-nav-type="thumb" aria-label="'.$value['caption'].'" style="width: '.$thumbwidth.'px" data-active="true" fast-image="yes" >
                                <div class="fotorama__thumb fotorama_vertical_ratio fotorama__loaded fotorama__loaded--img"><img src="'.$value['thumb'].'" alt="'.$value['caption'].'" aria-labelledby="labelledby1529299439448" class="fotorama__img" aria-hidden="false">
                                </div>
                            </div>';
                }else{
                    $mainThumbHtml.='<div class="fotorama__nav__frame fotorama__nav__frame--thumb" tabindex="0" role="button" data-gallery-role="nav-frame" data-nav-type="thumb" aria-label="'.$value['caption'].'" style="width: '.$thumbwidth.'px" data-active="true" fast-image="yes">
                                <div class="fotorama__thumb fotorama_vertical_ratio fotorama__loaded fotorama__loaded--img"><img src="'.$value['thumb'].'" alt="'.$value['caption'].'" aria-labelledby="labelledby1529299439448" class="fotorama__img" aria-hidden="false">
                                </div>
                            </div>';
                }
            }
        }
        return $mainThumbHtml;
    }

    public function getJsonWithMainImage($imagesJson)
    {
        $imagesData = json_decode($imagesJson,true);
        $isMain = false;
        foreach ($imagesData as $key => $value) {
            if (isset($value['isMain']) && $value['isMain'] == 1) {
                $isMain = true;
                break;
            }
        }
        if (!$isMain) {
            $firstImage = array_shift($imagesData);
            $firstImage['isMain'] = 1;
            array_unshift($imagesData, $firstImage);
            $imagesJson = json_encode($imagesData);
        }
        return $imagesJson;
    }

    public function getMainImage($imagesJson)
    {
        $imagesData = json_decode($imagesJson,true);

        $mainImage = [];

        if(count($imagesData)>0){
            foreach($imagesData as $key=>$value){
                if(isset($value['isMain']) && $value['isMain'] == 1){
                    $mainImage['img'] = $value['img'];
                    $mainImage['caption'] = $value['caption'];
                }
            }
        }
        return $mainImage;
    }
}
