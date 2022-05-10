<?php

namespace Edu\IU\VPCM\Rivet;

/**
 * @author Qiwen Zhu
 * @emial qiwzhu@iu.edu
 */

class Pagination {
    protected $pageKeyInGet = 'page';
    protected $baseUrl;
    protected $perPage = 9;
    protected $totalItems;
    protected $paginationLength = 5;
    protected $last;

    public function __construct(string $baseUrl, int $total, array $options = [])
    {
        $this->baseUrl = $baseUrl;
        $this->totalItems = $total;
        $this->perPage = isset($options['perPage']) ?? $this->perPage;
        $this->pageKeyInGet = isset($options['pageKey']) ?? $this->pageKeyInGet;
        $this->paginationLength = isset($options['paginationLength']) ?? $this->paginationLength;
        $this->last = $this->getTotalPageLinks();
    }


    /**
     * @return string
     * Return rendered pagination
     */
    public function render(): string
    {
        $first = $this->buildFirst();
        $prev = $this->buildPrevious();
        $last = $this->buildLast();
        $next = $this->buildNext();
        $pageLinks = $this->buildPageLinks();

        $html = <<< PAGINATION
<nav role="navigation" aria-label="More pages of items">
    <ul class="rvt-pagination">
    $first
    $prev
    $pageLinks
    $next
    $last
    </ul>
</nav>
PAGINATION;


        return $html;
    }



    protected function getCurrentPage(): int
    {
        return
        (isset($_GET[$this->pageKeyInGet]) && filter_var($_GET[$this->pageKeyInGet], FILTER_VALIDATE_INT))
            ?
            $_GET[$this->pageKeyInGet]
            :
            1;
    }

    protected function getCurrentUrl(): string
    {
        return '';
    }

    protected function getTotalPageLinks(): int
    {
        return  ceil($this->totalItems / $this->perPage);
    }




    public function buildPageLinks(): string
    {
        /**
         * << < 1 2 3 4 5 > >>
         */
        if($this->last == $this->paginationLength){
            for ($i = 0; $i < $this->paginationLength; $i ++){

            }
        }
        /**
         * << < 1 2 3 4 ... > >>
         */

        /**
         * << < ... 6 7 8 ... > >>
         */

        /**
         * << < ... last4 last3 last2 last > >>
         */
    }

    public function buildDots(string $url)
    {
        return <<< DOTS
<li class="rvt-pagination__item">
      <a href="$url" class="rvt-flex" tabindex="-1" aria-hidden="true">
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

    protected function buildNextLink()
    {
        $nextPage = $this->getCurrentPage() + 1;
        $oldPageQuery = $this->pageKeyInGet . '=' . $this->getCurrentPage();
        $newPageQuery = $this->pageKeyInGet . '=' . $nextPage;
        $url = str_replace($oldPageQuery, $newPageQuery, $this->getCurrentUrl());

        return $this->hasNext()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';
    }

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

    protected function buildLinkToLast(): string
    {
        $oldPageQuery = $this->pageKeyInGet . '=' . $this->getCurrentPage();
        $newPageQuery = $this->pageKeyInGet . '=' . $this->last;
        $url = str_replace($oldPageQuery, $newPageQuery, $this->getCurrentUrl());

        return $this->hasLast()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';

    }



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


    protected function buildPrevLink(): string
    {
        $prevPage = $this->getCurrentPage() - 1;
        $oldPageQuery = $this->pageKeyInGet . '=' . $this->getCurrentPage();
        $newPageQuery = $this->pageKeyInGet . '=' . $prevPage;
        $url = str_replace($oldPageQuery, $newPageQuery, $this->getCurrentUrl());

        return $this->hasPrev()
            ? '<a href="' . $url . '" aria-label="Go to next page">'
            : '';
    }

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

    public function buildLinkToFirst(): string
    {
        $oldPageQuery = $this->pageKeyInGet . '=' . $this->getCurrentPage();
        $newPageQuery = $this->pageKeyInGet . '=1';
        $url = str_replace($oldPageQuery, $newPageQuery, $this->getCurrentUrl());

        return $this->hasFirst()Set()
            ? '<a href="' . $url . '" aria-label="Go to first page">'
            : '';
    }


    //checkers

    private function hasPrev(): bool
    {
        return $this->getCurrentPage() != 1;
    }

    private function hasNext(): bool
    {
        return $this->getCurrentPage() != $this->last;
    }

    private function hasFirst(): bool
    {
        if(!$this->hasPrev()){
            return false;
        }

        return $this->getCurrentPage() < ($this->last - $this->paginationLength);
    }

    private function hasLast(): bool
    {
        if(!$this->hasNext()){
            return false;
        }

        return $this->getCurrentPage() > $this->paginationLength;
    }



}