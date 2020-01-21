<?php


namespace User\Thumb;


class Thumb
{

    protected $thumb;

    public function __construct($thumb)
    {

        $this->thumb = $thumb;
    }

    public function geraThumb($nomeImg)
    {

        // gerar thumb **/

        $path = 'public/media/'.$nomeImg;

        //--tamanho--864x410--
        $thumb864x410   = $this->thumb;
        $thumbnail_864x410   = $thumb864x410->create($path, $options = [], $plugins = []);
        $thumbnail_864x410->adaptiveResize(640, 426);
        $thumbnail_864x410->save('public/media/thumb/640x426-'.$nomeImg);

        //--tamanho--576x515--
        $thumb576x515   = $this->thumb;
        $thumbnail_576x515   = $thumb576x515->create($path, $options = [], $plugins = []);
        $thumbnail_576x515->adaptiveResize(300, 350);
        $thumbnail_576x515->save('public/media/thumb/300x350-'.$nomeImg);

        //--tamanho--864x259--
        $thumb864x259   = $this->thumb;
        $thumbnail_864x259   = $thumb864x259->create($path, $options = [], $plugins = []);
        $thumbnail_864x259->adaptiveResize(100, 117);
        $thumbnail_864x259->save('public/media/thumb/100x117-'.$nomeImg);

        //padrao admin

        //--tamanho--50x50--
        $thumb50x50   = $this->thumb;
        $thumbnail_50x50   = $thumb50x50->create($path, $options = [], $plugins = []);
        $thumbnail_50x50->adaptiveResize(50, 50);
        $thumbnail_50x50->save('public/media/thumb/50x50-'.$nomeImg);

        //--tamanho--768x547--
        $thumb768x547   = $this->thumb;
        $thumbnail_768x547   = $thumb768x547->create($path, $options = [], $plugins = []);
        $thumbnail_768x547->adaptiveResize(768, 547);
        $thumbnail_768x547->save('public/media/thumb/768x547-'.$nomeImg);


        return $this;
    }

    public function removeThumb($nomeImg)
    {

        unlink('public/media/thumb/864x410-'.$nomeImg);
        unlink('public/media/thumb/576x515-'.$nomeImg);
        unlink('public/media/thumb/864x259-'.$nomeImg);
        unlink('public/media/thumb/50x50-'.$nomeImg);
        unlink('public/media/thumb/768x547-'.$nomeImg);


    }

}