<?php
    
namespace App\Service;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Michelf\MarkdownInterface;
use Psr\Log\LoggerInterface;
 

class MarkdownHelper
{   
    private $cache;
    private $markdown;
    private $logger;
    private $isDebug;

    public function __construct(AdapterInterface $cache, MarkdownInterface $markdown, LoggerInterface $markdownLogger, bool $isDebug)
    {
        $this->cache = $cache;
        $this->markdown = $markdown;
        $this->logger = $markdownLogger;
        $this->isDebug = $isDebug;
    }


    public function parse(string $source):string{

        //check if 'bacon' string is in the markdown $source
        if(strpos($source, 'bacon') !== false){
            $this->logger->info('They are talking about bacon again...');
        }

        if($this->isDebug)
        {   
            //if we r in dev mode, return the uncaches source
            return $this->markdown->transform($source);
        }
        
        dump($this->cache); die;
         //cache markdown conversion (we pass a markdownkey to getItem() )
         $item = $this->cache->getItem('markdown_'.md5($source));
         //check if key is not aready cached
         if(!$item->isHit()) {
                 //set the key (and transform the markdown)
                 $item->set($this->markdown->transform($source));
                 //save the key to the cache
                 $this->cache->save($item);
         }

         // fetch from the cache
         return $item->get();
    }
}

?>