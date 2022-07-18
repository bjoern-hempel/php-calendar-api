<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Config;

use App\Utils\GPSConverter;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SearchConfig
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-07-16)
 * @since 1.0.1 (2022-07-16) Fix empty current request for cli commands.
 * @since 1.0.0 (2022-07-03) First version.
 * @package App\Config
 */
class SearchConfig
{
    protected ?Request $request;

    protected ?string $error;

    /* The id string like a:189454, etc. */
    protected ?string $idString;

    /* The current location like 51.061182,13.740584, etc. */
    /** @var float[]|null  */
    protected ?array $location;

    /* The number of search items per page. */
    protected int $numberPerPage;

    /* The number of results. */
    protected int $numberResults;

    /* The current visible page like 1, etc. */
    protected int $page;

    /* The search query like "Dresden", etc. */
    protected ?string $searchQuery;

    /* The sort order of search list. */
    protected string $sort;

    /* Verbose mode */
    protected bool $verbose = false;

    public const ORDER_BY_LOCATION = 'l';
    public const ORDER_BY_NAME = 'n';
    public const ORDER_BY_RELEVANCE = 'r';
    public const ORDER_BY_RELEVANCE_LOCATION = 'rl';

    public const VIEW_MODE_SEARCH = 0;
    public const VIEW_MODE_LIST = 1;
    public const VIEW_MODE_DETAIL = 2;
    public const VIEW_MODE_CURRENT_POSITION = 3;

    public const PARAMETER_NAME_ERROR = 'e';
    public const PARAMETER_DEFAULT_ERROR = null;

    public const PARAMETER_NAME_ID_STRING = 'id';
    public const PARAMETER_DEFAULT_ID_STRING = null;

    public const PARAMETER_NAME_LOCATION = 'l';
    public const PARAMETER_DEFAULT_LOCATION = null;

    public const PARAMETER_NAME_NUMBER_PER_PAGE = 'n';
    public const PARAMETER_DEFAULT_NUMBER_PER_PAGE = 10;

    public const PARAMETER_NAME_NUMBER_RESULTS = 'r';
    public const PARAMETER_DEFAULT_NUMBER_RESULTS = 0;

    public const PARAMETER_NAME_PAGE = 'p';
    public const PARAMETER_DEFAULT_PAGE = 1;

    public const PARAMETER_NAME_SEARCH_QUERY = 'q';
    public const PARAMETER_DEFAULT_SEARCH_QUERY = null;

    public const PARAMETER_NAME_SORT = 's';
    public const PARAMETER_DEFAULT_SORT = self::ORDER_BY_RELEVANCE;

    public const PARAMETER_NAME_VERBOSE = 'v';
    public const PARAMETER_DEFAULT_VERBOSE = false;

    /**
     * SearchConfig constructor.
     *
     * @param RequestStack $requestStack
     * @throws Exception
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();

        /* Request all parameters. */
        $this->requestParameters();
    }

    /**
     * Requests all necessary parameters.
     *
     * @return void
     * @throws Exception
     */
    protected function requestParameters(): void
    {
        if ($this->request === null) {
            return;
        }

        $this->setError(null);
        $this->setIdString(
            $this->request->query->has(self::PARAMETER_NAME_ID_STRING) ?
                strval($this->request->query->get(self::PARAMETER_NAME_ID_STRING)) :
                self::PARAMETER_DEFAULT_ID_STRING
        );
        $this->setLocationString(
            $this->request->query->has(self::PARAMETER_NAME_LOCATION) ?
                strval($this->request->query->get(self::PARAMETER_NAME_LOCATION)) :
                self::PARAMETER_DEFAULT_LOCATION
        );
        $this->setNumberPerPage(
            $this->request->query->has(self::PARAMETER_NAME_NUMBER_PER_PAGE) ?
                intval($this->request->query->get(self::PARAMETER_NAME_NUMBER_PER_PAGE)) :
                self::PARAMETER_DEFAULT_NUMBER_PER_PAGE
        );
        $this->setNumberResults(self::PARAMETER_DEFAULT_NUMBER_RESULTS);
        $this->setPage(
            $this->request->query->has(self::PARAMETER_NAME_PAGE) ?
                intval($this->request->query->get(self::PARAMETER_NAME_PAGE)) :
                self::PARAMETER_DEFAULT_PAGE
        );
        $this->setSearchQuery(
            $this->request->query->has(self::PARAMETER_NAME_SEARCH_QUERY) ?
                strval($this->request->query->get(self::PARAMETER_NAME_SEARCH_QUERY)) :
                self::PARAMETER_DEFAULT_SEARCH_QUERY
        );
        $this->setSort(
            $this->request->query->has(self::PARAMETER_NAME_SORT) ?
                strval($this->request->query->get(self::PARAMETER_NAME_SORT)) :
                self::PARAMETER_DEFAULT_SORT
        );
        $this->setVerbose(
            $this->request->query->has(self::PARAMETER_NAME_VERBOSE) ?
                boolval($this->request->query->get(self::PARAMETER_NAME_VERBOSE)) :
                self::PARAMETER_DEFAULT_VERBOSE
        );
    }

    /**
     * Returns all parameters as array.
     *
     * @return int[]|string[]|bool[]|null[]
     */
    #[ArrayShape([
        self::PARAMETER_NAME_ERROR => "null|string",
        self::PARAMETER_NAME_ID_STRING => "null|string",
        self::PARAMETER_NAME_LOCATION => "null|string",
        self::PARAMETER_NAME_NUMBER_PER_PAGE => "int",
        self::PARAMETER_NAME_PAGE => "int",
        self::PARAMETER_NAME_SEARCH_QUERY => "null|string",
        self::PARAMETER_NAME_SORT => "string",
        self::PARAMETER_NAME_VERBOSE => "bool"
    ])]
    public function getParameterArray(): array
    {
        return [
            self::PARAMETER_NAME_ERROR => $this->getError(),
            self::PARAMETER_NAME_ID_STRING => $this->getIdString(),
            self::PARAMETER_NAME_LOCATION => $this->getLocationString(),
            self::PARAMETER_NAME_NUMBER_PER_PAGE => $this->getNumberPerPage(),
            self::PARAMETER_NAME_PAGE => $this->getPage(),
            self::PARAMETER_NAME_SEARCH_QUERY => $this->getSearchQuery(),
            self::PARAMETER_NAME_SORT => $this->getSort(),
            self::PARAMETER_NAME_VERBOSE => $this->isVerbose(),
        ];
    }

    /**
     * Gets error of this search request.
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Sets error of this search request.
     *
     * @param string|null $error
     * @return $this
     */
    public function setError(?string $error): self
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Returns the id string.
     *
     * @return string|null
     */
    public function getIdString(): ?string
    {
        return $this->idString;
    }

    /**
     * Returns the id string.
     *
     * @return bool
     */
    public function hasIdString(): bool
    {
        return !is_null($this->idString);
    }

    /**
     * Returns the id string.
     *
     * @param string|null $idString
     * @return $this
     */
    public function setIdString(?string $idString): self
    {
        $this->idString = $idString;

        return $this;
    }

    /**
     * Returns the current location of user.
     *
     * @return float[]|null
     */
    public function getLocation(): ?array
    {
        return $this->location;
    }

    /**
     * Returns if the current location of user is available.
     *
     * @return bool
     */
    public function hasLocation(): bool
    {
        return !is_null($this->location);
    }

    /**
     * Sets the current location of user.
     *
     * @param float[]|null $location
     * @return $this
     */
    public function setLocation(?array $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Returns the current location of user.
     *
     * @return string|null
     */
    public function getLocationString(): ?string
    {
        if (is_null($this->location)) {
            return null;
        }

        return implode(',', $this->location);
    }

    /**
     * Sets the current location of user.
     *
     * @param string|null $location
     * @return $this
     * @throws Exception
     */
    public function setLocationString(?string $location): self
    {
        if ($location === null) {
            $this->location = null;

            return $this;
        }

        $parsedLocation = GPSConverter::parseFullLocation2DecimalDegrees($location);

        if ($parsedLocation === false) {
            throw new Exception(sprintf('Unable to parse location "%s" (%s:%d).', $location, __FILE__, __LINE__));
        }

        $this->location = $parsedLocation;

        return $this;
    }

    /**
     * Gets the number of search items per page.
     *
     * @return int
     */
    public function getNumberPerPage(): int
    {
        return $this->numberPerPage;
    }

    /**
     * Sets the number of search items per page.
     *
     * @param int $numberPerPage
     * @return $this
     */
    public function setNumberPerPage(int $numberPerPage): self
    {
        $this->numberPerPage = $numberPerPage;

        return $this;
    }

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNumberResults(): int
    {
        return $this->numberResults;
    }

    /**
     * Sets the number of results.
     *
     * @param int $numberResults
     * @return $this
     */
    public function setNumberResults(int $numberResults): self
    {
        $this->numberResults = $numberResults;

        return $this;
    }

    /**
     * Returns the given page.
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Returns the given page.
     *
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Returns the search query.
     *
     * @return string|null
     */
    public function getSearchQuery(): ?string
    {
        return $this->searchQuery;
    }

    /**
     * Returns if search query is available.
     *
     * @return bool
     */
    public function hasSearchQuery(): bool
    {
        return !is_null($this->searchQuery);
    }

    /**
     * Sets the search query.
     *
     * @param string|null $searchQuery
     * @return $this
     * @throws Exception
     */
    public function setSearchQuery(?string $searchQuery): self
    {
        $this->searchQuery = null;

        if ($searchQuery === null) {
            return $this;
        }

        $searchQuery = trim($searchQuery);

        /* ID string was found. */
        if (preg_match('~^[ahlprstuv]:\d+$~', $searchQuery)) {
            $this->setIdString($searchQuery);
            return $this;
        }

        $locationParsed = GPSConverter::parseFullLocation2DecimalDegrees($searchQuery);

        if ($locationParsed !== false) {
            $this->setLocation($locationParsed);
            return $this;
        }

        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * Returns the sort mode.
     *
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * Sets the sort mode.
     *
     * Possible sort modes:
     *
     * r - relevance
     * n - name
     * l - location (needs $location !== 0)
     * rl - relevance and location (needs $location !== 0)
     *
     * @param string $sort
     * @return $this
     * @throws Exception
     */
    public function setSort(string $sort): self
    {
        if (!in_array($sort, [self::ORDER_BY_LOCATION, self::ORDER_BY_NAME, self::ORDER_BY_RELEVANCE, self::ORDER_BY_RELEVANCE_LOCATION])) {
            throw new Exception(sprintf('Unsupported sort mode "%s" (%s:%d).', $sort, __FILE__, __LINE__));
        }

        $this->sort = $sort;

        return $this;
    }

    /**
     * Gets the verbose mode.
     *
     * @return bool
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Sets the verbose parameter.
     *
     * @param bool $verbose
     * @return $this
     */
    public function setVerbose(bool $verbose): self
    {
        $this->verbose = $verbose;

        return $this;
    }

    /**
     * Returns the current request.
     *
     * @return Request
     * @throws Exception
     */
    public function getRequest(): Request
    {
        if ($this->request === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        return $this->request;
    }

    /**
     * Returns the mode of this search:
     *
     * 0 - No search, empty search, error
     * 1 - list
     * 2 - detail
     * 3 - current location search
     *
     * @return int
     */
    public function getViewMode(): int
    {
        if ($this->getError() !== null) {
            return self::VIEW_MODE_SEARCH; // 0
        }

        return match (true) {
            $this->getIdString() !== null => self::VIEW_MODE_DETAIL, // 2
            $this->getSearchQuery() !== null => self::VIEW_MODE_LIST, // 1
            $this->getLocation() !== null => self::VIEW_MODE_CURRENT_POSITION, // 3
            default => self::VIEW_MODE_SEARCH, // 0
        };
    }

    /**
     * Returns all needed search input form elements.
     *
     * @return string
     */
    public function getInputs(): string
    {
        $search = match (true) {
            !is_null($this->getSearchQuery()) => $this->getSearchQuery(),
            !is_null($this->getIdString()) => $this->getIdString(),
            !is_null($this->getLocationString()) => $this->getLocationString(),
            default => '',
        };

        $inputs = sprintf(
            '<input type="search" id="%s" name="%s" required="required" autofocus="autofocus" value="%s">',
            self::PARAMETER_NAME_SEARCH_QUERY,
            self::PARAMETER_NAME_SEARCH_QUERY,
            $search
        );

        $inputs .= sprintf(
            '<input type="hidden" id="%s" name="%s" value="%s">',
            self::PARAMETER_NAME_SORT,
            self::PARAMETER_NAME_SORT,
            $this->getSort()
        );

        $inputs .= sprintf(
            '<input type="hidden" id="%s" name="%s" value="%s">',
            self::PARAMETER_NAME_PAGE,
            self::PARAMETER_NAME_PAGE,
            $this->getPage()
        );

        if ($this->getLocationString() !== null) {
            $inputs .= sprintf(
                '<input type="hidden" id="%s" name="%s" value="%s">',
                self::PARAMETER_NAME_LOCATION,
                self::PARAMETER_NAME_LOCATION,
                $this->getLocationString()
            );
        }

        if ($this->isVerbose()) {
            $inputs .= sprintf(
                '<input type="hidden" id="%s" name="%s" value="1">',
                self::PARAMETER_NAME_VERBOSE,
                self::PARAMETER_NAME_VERBOSE
            );
        }

        return $inputs;
    }

    /**
     * Returns the next page.
     *
     * @return int|null
     */
    public function getNextPage(): ?int
    {
        $nextPage = null;

        if ($this->getViewMode() !== self::VIEW_MODE_LIST) {
            return null;
        }

        $numberLastElement = min($this->getPage() * $this->getNumberPerPage(), $this->getNumberResults());

        if ($this->getNumberResults() > $numberLastElement) {
            $nextPage = $this->getPage() + 1;
        }

        return $nextPage;
    }
}
