<?php
/**
 * WechatResponse 用于微信信息回复
 * @author linyh
 */
import('View');
import('Widget');
class WechatView extends View {
    public function textScene($content){
//        trigger_error('建议使用$this->normalScene(new WechatTextWidget($content));',E_USER_NOTICE);
        $this->normalScene(new WechatTextWidget($content));
    }

    public function nullScene(){
        echo "";
    }

    public function normalScene(Widget $widget){
        $s=<<<xmlString
<xml>
<ToUserName><![CDATA[{$_POST['FromUserName']}]]></ToUserName>
<FromUserName><![CDATA[{$_POST['ToUserName']}]]></FromUserName>
<CreateTime>{$_SERVER["REQUEST_TIME"]}</CreateTime>
<MsgType><![CDATA[{$widget->type}]]></MsgType>
{$widget}
</xml>
xmlString;
        echo $s;
    }
}

class WechatTextWidget extends Widget {
    protected $content;
    public $type="text";

    function __construct($content) {
        $this->content = $content;
    }

    public function __toString() {
        $s="<Content><![CDATA[{$this->content}]]></Content>";
        return $s;
    }
}

class WechatMusicWidget extends Widget {
    protected $title, $description, $url, $hqUrl, $mediaId;
    public $type="music";

    function __construct($title, $description, $url, $hqUrl, $mediaId) {
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->hqUrl = $hqUrl;
        $this->mediaId = $mediaId;
    }

    public function __toString() {
        $s=<<<xmlString
<Music>
<Title><![CDATA[{$this->title}]]></Title>
<Description><![CDATA[{$this->description}]]></Description>
<MusicUrl><![CDATA[{$this->url}]]></MusicUrl>
<HQMusicUrl><![CDATA[{$this->hqUrl}]]></HQMusicUrl>
<ThumbMediaId><![CDATA[{$this->mediaId}]]></ThumbMediaId>
</Music>
xmlString;
        return $s;
    }
}

class WechatNewsWidget extends Widget {
    protected $newsArray;
    public $type="news";

    /**
     * 添加一条新闻
     * @param string $title 标题
     * @param string $description 描述
     * @param string $url 链接
     * @param string $picUrl 图片链接
     */
    public function addNews($title, $description, $url, $picUrl=null){
        $this->newsArray[]=array(
            'title'=>$title,
            'description'=>$description,
            'url'=>$url,
            'pic_url'=>$picUrl
        );
    }

    /**
     * 二位数组要求每一行数据键名为title,description,url,pic_url，否则请自行制定
     * @param $newsArray
     * @param string $titleKey
     * @param string $descriptionKey
     * @param string $urlKey
     * @param string $picUrlKey
     */
    public function addNewsArray($newsArray, $titleKey='title', $descriptionKey='description',
                                 $urlKey='url', $picUrlKey='pic_url'){
        foreach($newsArray as $v){
            $this->newsArray[]=array(
                'title'=>$v[$titleKey],
                'description'=>$v[$descriptionKey],
                'url'=>$v[$urlKey],
                'pic_url'=>$v[$picUrlKey]
            );
        }
    }

    public function __toString() {
        $newsCount=count($this->newsArray);
        $s="<ArticleCount>$newsCount</ArticleCount><Articles>";
        foreach($this->newsArray as $v){
            $s.=<<<newsArray
<item>
    <Title><![CDATA[{$v['title']}]]></Title>
    <Description><![CDATA[{$v['description']}]]></Description>
    <PicUrl><![CDATA[{$v['pic_url']}]]></PicUrl>
    <Url><![CDATA[{$v['url']}]]></Url>
</item>
newsArray;
        }
        $s.="</Articles>";
        return $s;
    }
}
