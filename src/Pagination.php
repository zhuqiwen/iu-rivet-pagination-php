<?php

namespace Edu\IU\VPCM\Rivet;

/**
 * @author Qiwen Zhu
 * @emial qiwzhu@iu.edu
 */

class Pagination {
    /**
     * @var mixed|string
     */
    protected $pageKeyInGet = 'page';
    /**
     * @var array|mixed|string|string[]
     */
    protected $queryString;
    /**
     * @var int|mixed
     */
    protected $perPage = 9;
    /**
     * @var int
     */
    protected $totalItems;
    /**
     * @var int|mixed
     */
    protected $paginationLength = 5;
    /**
     * @var int
     */
    protected $last;
    /**
     * @var mixed|string
     */
    protected $rivetVersion = 'v2';
    /**
     * @var array|mixed
     */
    protected $rivetV1Settings;

    /**
     * @param int $total
     * @param array $options
     */
    public function __construct(int $total, array $options = [])
    {
        $this->totalItems = $total;

        $this->queryString = isset($options['queryString']) ? str_replace('?', '', $options['queryString']) : $_SERVER['QUERY_STRING'];
        $this->rivetVersion = isset($options['rivetVersion']) ? $options['rivetVersion'] : $this->rivetVersion;
        $this->perPage = isset($options['perPage']) ? $options['perPage'] : $this->perPage;
        $this->pageKeyInGet = isset($options['pageKeyInGet']) ? $options['pageKeyInGet'] : $this->pageKeyInGet;
        $this->paginationLength = isset($options['paginationLength']) ? $options['paginationLength'] : $this->paginationLength;
        $this->rivetV1Settings = isset($options['rivetVersion']) && $options['rivetVersion'] == 'v1' && isset($options['rivetV1Settings'])
            ? $options['rivetV1Settings'] : [];
        $this->last = $this->getTotalPageLinks();
    }


    /**
     * @return string
     * Return rendered pagination
     */
    public function render(): string
    {
        $method = 'render' . $this->rivetVersion;
        $html = 'some error message';
        if(method_exists($this, $method)){
            $html = $this->$method();
        }

        return $html;
    }


    /**
     * @return string
     */
    public function renderV1()
    {
        $prev = $this->buildPreviousV1();
        $next = $this->buildNextV1();
        $pageLinks = $this->buildPageLinksV1();
        $isSmall =
            isset($this->rivetV1Settings['size']) && $this->rivetV1Settings['size'] == 'small'
                ?
                'rvt-pagination--small' : '';
        $position =
            !isset($this->rivetV1Settings['position']) ?
                ''
                :
                (
                    $this->rivetV1Settings['position'] == 'right' ?
                        'rvt-pagination--right'
                        :
                        (
                            $this->rivetV1Settings['position'] == 'center' ?
                                'rvt-pagination--center' : ''
                        )
                );

        $html = <<< V1
<nav role="navigation" aria-label="More pages of items">
    <ul class="rvt-pagination $isSmall $position">
        $prev 
        $pageLinks 
        $next
    </ul>
</nav>
V1;

        return $html;

    }

    /**
     * @return string
     */
    public function buildFirstV1(): string
    {
        return $this->buildSingleLinkV1(1);
    }

    /**
     * @return string
     */
    public function buildPreviousV1(): string
    {
        $link = $this->getNewUrl($this->getCurrentPage() - 1);
        $html = <<< PREVV1
<li class="rvt-pagination__item">
  <a href="$link" aria-label="Previous page">Previous</a>
</li>
PREVV1;

        return $this->hasPrev() ? $html : '';
    }


    /**
     * @return string
     */
    public function buildLastV1(): string
    {
        return $this->buildSingleLinkV1($this->last);
    }


    /**
     * @return string
     */
    public function buildNextV1(): string
    {
        $link = $this->getNewUrl($this->getCurrentPage() + 1);
        $html = <<< NEXTV1
<li class="rvt-pagination__item">
  <a href="$link" arial-label="Next page">Next</a>
</li>
NEXTV1;

        return $this->hasNext() ? $html : '';
    }


    /**
     * @return string
     */
    public function buildPageLinksV1(): string
    {
        $html = '';
        /**
         * 1 2 3 4 5
         */
        if($this->last <= $this->paginationLength){
            for ($i = 1; $i <= $this->last; $i ++){
                $html .= $this->buildSingleLinkV1($i);
            }
        }else{
            /**
             * 1 2 3 4 ... last
             */
            if($this->getCurrentPage() < $this->paginationLength){
                for($i = 1; $i < $this->paginationLength; $i++){
                    $html .= $this->buildSingleLinkV1($i);
                }
                $html .= $this->buildDotsV1($this->paginationLength);
                $html .= $this->buildLastV1();
            }
            /**
             * 1 ... last4 last3 last2 last
             */
            elseif($this->getCurrentPage() > $this->last - $this->paginationLength + 1){
                $html .= $this->buildFirstV1();
                $html .= $this->buildDotsV1($this->last - $this->paginationLength + 1);
                for ($i = 1; $i < $this->paginationLength; $i++){
                    $pageNum = $this->last - $this->paginationLength + 1 + $i;
                    $html .= $this->buildSingleLinkV1($pageNum);
                }
            }
            /**
             * 1 ... 6 7 8 ... last
             */
            else{
                $html .= $this->buildFirstV1();
                $html .= $this->buildDotsV1($this->getCurrentPage() - $this->paginationLength + 2);
                for($i = 0; $i < $this->paginationLength - 2; $i++){
                    $pageNum = $this->getCurrentPage() + $i;
                    $html .= $this->buildSingleLinkV1($pageNum);
                }
                $html .= $this->buildDotsV1($this->getCurrentPage() + $this->paginationLength - 2);
                $html .= $this->buildLastV1();
            }
        }

        return $html;

    }


    /**
     * @param int $pageNum
     * @return string
     */
    public function buildSingleLinkV1(int $pageNum): string
    {
        $url = $this->getNewUrl($pageNum);
        $current = $pageNum == $this->getCurrentPage() ? 'aria-current="true"' : '';
        $isActive = $pageNum == $this->getCurrentPage() ? 'is-active' : '';
        $arialLabel = 'Page ' . $pageNum;
        $arialLabel .= $pageNum == $this->last ? ', last page' : '';
        $arialLabel .= $pageNum == 1 ? ', first page' : '';
        $arialLabel .= $pageNum == $this->getCurrentPage() ? ', current page' : '';
        $html = <<< LINKV1
<li class="rvt-pagination__item $isActive" >
    <a href="$url" aria-label="$arialLabel" $current>$pageNum</a>
</li>
LINKV1;

        return $html;
    }


    /**
     * @param int $pageNum
     * @return string
     */
    public function buildDotsV1(int $pageNum)
    {
        return $this->buildDots($pageNum);
    }


    /**
     * @return string
     */
    public function renderV2()
    {
        $first = $this->buildFirst();
        $prev = $this->buildPrevious();
        $last = $this->buildLast();
        $next = $this->buildNext();
        $pageLinks = $this->buildPageLinks();

        $html = <<< V2
<nav role="navigation" aria-label="More pages of items">
    <ul class="rvt-pagination">
        $first 
        $prev 
        $pageLinks 
        $next 
        $last
    </ul>
</nav>
V2;

        return $html;
    }

    /**
     * @return string
     */
    public function buildPageLinks(): string
    {
        $html = '';
        /**
         * << < 1 2 3 4 5 > >>
         */
        if($this->last <= $this->paginationLength){
            for ($i = 1; $i <= $this->last; $i ++){
                $html .= $this->buildSingleLink($i);
            }
        }else{
            /**
             * << < 1 2 3 4 ... > >>
             */
            if($this->getCurrentPage() < $this->paginationLength){
                for($i = 1; $i < $this->paginationLength; $i++){
                    $html .= $this->buildSingleLink($i);
                }

                $html .= $this->buildDots($this->paginationLength);
            }
            /**
             * << < ... last4 last3 last2 last > >>
             */
            elseif($this->getCurrentPage() > $this->last - $this->paginationLength + 1){
                $html .= $this->buildDots($this->last - $this->paginationLength + 1);
                for ($i = 1; $i < $this->paginationLength; $i++){
                    $pageNum = $this->last - $this->paginationLength + 1 + $i;
                    $html .= $this->buildSingleLink($pageNum);
                }
            }
            /**
             * << < ... 6 7 8 ... > >>
             */
            else{
                $html .= $this->buildDots($this->getCurrentPage() - $this->paginationLength + 2);
                for($i = 0; $i < $this->paginationLength - 2; $i++){
                    $pageNum = $this->getCurrentPage() + $i;
                    $html .= $this->buildSingleLink($pageNum);
                }
                $html .= $this->buildDots($this->getCurrentPage() + $this->paginationLength - 2);
            }
        }

        return $html;
    }

    /**
     * @param int $pageNum
     * @return string
     */
    public function buildSingleLink(int $pageNum): string
    {
        $url = $this->getNewUrl($pageNum);
        $current = $pageNum == $this->getCurrentPage() ? 'aria-current="page"' : '';
        $html = <<< LINK
<li class="rvt-pagination__item">
    <a href="$url" aria-label="Page $pageNum" $current>$pageNum</a>
</li>
LINK;

        return $html;
    }

    /**
     * @param int $pageNum
     * @return string
     */
    public function buildDots(int $pageNum)
    {
        $url = $this->getNewUrl($pageNum);

        return <<< DOTS
<li class="rvt-pagination__item">
      <a href="$url" class="rvt-flex" aria-label="Skip to page $pageNum">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
          <g fill="currentColor">
            <circle cx="8" cy="8" r="2"></circle>
            <circle cx="14" cy="8" r="2"></circle>
            <circle cx="2" cy="8" r="2"></circle>
          </g>
        </svg>
      </a>
    </li>
DOTS;


    }

    /**
     * @return string
     */
    protected function buildNext(): string
    {
        $v2Link = $this->buildNextLink();
        $v2NoNext = $this->hasNext() ? '' : 'aria-label="No next page"';
        $v2 = <<< NEXT
<li class="rvt-pagination__item" $v2NoNext>
    $v2Link
    <svg width="16" height="16" viewBox="0 0 16 16">
      <path fill="currentColor" d="M5.5,15a1,1,0,0,1-.77-1.64L9.2,8,4.73,2.64A1,1,0,0,1,6.27,1.36L11.13,7.2a1.25,1.25,0,0,1,0,1.61L6.27,14.64A1,1,0,0,1,5.5,15ZM9.6,8.48h0Zm0-1h0Z"/>
    </svg>
  </a>
</li>
NEXT;

        return $v2;
    }

    /**
     * @return string
     */
    protected function buildNextLink()
    {
        $url = $this->getNewUrl($this->getCurrentPage() + 1);

        return $this->hasNext()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';
    }

    /**
     * @return string
     */
    protected function buildLast(): string
    {
        $v2Link = $this->buildLinkToLast();
        $v2NoNext = $this->hasNext() ? '' : 'aria-label="No next pages"';
        $v2 = <<< NEXTSET
<li class="rvt-pagination__item" $v2NoNext>
  $v2Link
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
      <g fill="currentColor">
        <path d="M3,13.8a1,1,0,0,1-.77-1.64L5.7,8,2.23,3.84A1,1,0,0,1,3.77,2.56L7.63,7.2a1.25,1.25,0,0,1,0,1.61L3.77,13.44A1,1,0,0,1,3,13.8ZM6.1,8.48h0Zm0-1h0Z"></path>
        <path d="M9,13.8a1,1,0,0,1-.77-1.64L11.7,8,8.23,3.84A1,1,0,0,1,9.77,2.56L13.63,7.2a1.25,1.25,0,0,1,0,1.61L9.77,13.44A1,1,0,0,1,9,13.8Zm3.1-5.32h0Zm0-1h0Z"></path>
      </g>
    </svg>
  </a>
</li>
NEXTSET;

        return $v2;
    }

    /**
     * @return string
     */
    protected function buildLinkToLast(): string
    {
        $url = $this->getNewUrl($this->last);

        return $this->hasNext()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';

    }


    /**
     * @return string
     */
    protected function buildPrevious(): string
    {
        $v2Link = $this->buildPrevLink();
        $v2NoPrev = $this->hasPrev() ? '' : 'aria-label="No previous pages"';
        $v2 = <<< PREV
<li class="rvt-pagination__item" $v2NoPrev>
  $v2Link
    <svg width="16" height="16" viewBox="0 0 16 16">
      <path fill="currentColor" d="M10.5,15a1,1,0,0,1-.77-.36L4.87,8.8a1.25,1.25,0,0,1,0-1.61L9.73,1.36a1,1,0,0,1,1.54,1.28L6.8,8l4.47,5.36A1,1,0,0,1,10.5,15ZM6.41,8.47h0Zm0-1h0Z"></path>
    </svg>
  </a>
</li>
PREV;

        return $v2;
    }


    /**
     * @return string
     */
    protected function buildPrevLink(): string
    {
        $url = $this->getNewUrl($this->getCurrentPage() - 1);

        return $this->hasPrev()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';
    }

    /**
     * @return string
     */
    protected function buildFirst(): string
    {
        $v2Link = $this->buildLinkToFirst();
        $v2NoPrev = $this->hasPrev() ? '' : 'aria-label="No previous pages"';
        $v2 = <<< PREVSET
<li class="rvt-pagination__item" $v2NoPrev>  
    $v2Link
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
        <g fill="currentColor">
          <path d="M13,13.8a1,1,0,0,1-.77-.36L8.37,8.8a1.25,1.25,0,0,1,0-1.61l3.86-4.64a1,1,0,0,1,1.54,1.28L10.3,8l3.47,4.16A1,1,0,0,1,13,13.8ZM9.91,8.47h0Zm0-1h0Z"/>
          <path d="M7,13.8a1,1,0,0,1-.77-.36L2.37,8.8a1.25,1.25,0,0,1,0-1.61L6.23,2.56A1,1,0,0,1,7.77,3.84L4.3,8l3.47,4.16A1,1,0,0,1,7,13.8ZM3.91,8.47h0Zm0-1h0Z"/>
        </g>
      </svg>
    </li>
PREVSET;

        return $v2;
    }

    /**
     * @return string
     */
    public function buildLinkToFirst(): string
    {
        $url = $this->getNewUrl(1);

        return $this->hasPrev()
            ? '<a href="' . $url . '" aria-label="Go to first page">'
            : '';
    }


    //checkers

    /**
     * @return bool
     */
    private function hasPrev(): bool
    {
        return $this->getCurrentPage() != 1;
    }

    /**
     * @return bool
     */
    private function hasNext(): bool
    {
        return $this->getCurrentPage() != $this->last;
    }


    //getters

    /**
     * @return int
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * @return int|mixed
     */
    public function getPaginationLength()
    {
        return $this->paginationLength;
    }


    //helpers

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return
            (isset($_GET[$this->pageKeyInGet]) && filter_var($_GET[$this->pageKeyInGet], FILTER_VALIDATE_INT))
                ?
                $_GET[$this->pageKeyInGet]
                :
                1;
    }

    /**
     * @param int $pageNum
     * @return string
     */
    public function getNewUrl(int $pageNum)
    {
        $hostAndPath = explode('?', $_SERVER['REQUEST_URI'])[0];
        parse_str($this->queryString, $params);
        unset($params[$this->pageKeyInGet]);
        $params[$this->pageKeyInGet] = $pageNum;
        $newQuery = http_build_query($params);

        return implode('?', [$hostAndPath, $newQuery]);

    }

    /**
     * @return int
     */
    protected function getTotalPageLinks(): int
    {
        return  ceil($this->totalItems / $this->perPage);
    }

}