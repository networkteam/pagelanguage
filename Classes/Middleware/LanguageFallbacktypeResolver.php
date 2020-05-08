<?php
namespace Networkteam\Pagelanguage\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Set language fallback type by page field.
 *
 * Before TYPO3 9.5 Site Handling it was possible to use something like
 *
 * [globalVar = TSFE:id=1] && [globalVar = GP:L=2]
 *   config {
 *     sys_language_mode = ignore
 *   }
 * [global]
 *
 * to have an individual language handling. See also https://forge.typo3.org/issues/86712
 */
class LanguageFallbacktypeResolver implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var SiteLanguage $language */
        $language = $request->getAttribute('language');
        if ($language->getLanguageId() > 0) {
            $pageId = $this->getRequestedPageId($request);
            if ($pageId && $pageId > 0) {
                $fallbackType = $this->getFallbackType($pageId, $language->getLanguageId());
                if (!empty($fallbackType)) {
                    $language = $this->setLanguageFallback($language, $fallbackType);
                }
                $request = $request->withAttribute('language', $language);
            }
        }
        return $handler->handle($request);
    }

    protected function setLanguageFallback(SiteLanguage $siteLanguage, string $fallbackType): SiteLanguage
    {
        $configuration = $siteLanguage->toArray();
        $configuration['fallbackType'] = $fallbackType;
        $newSiteLanguage = new SiteLanguage (
            $siteLanguage->getLanguageId(),
            $siteLanguage->getLocale(),
            $siteLanguage->getBase(),
            $configuration
        );
        return $newSiteLanguage;
    }

    protected function getRequestedPageId(ServerRequestInterface $request)
    {
        $site = $request->getAttribute('site');
        $previousResult = $request->getAttribute('routing', null);
        $pageArguments = $site->getRouter()->matchRequest($request, $previousResult);
        if ($pageArguments instanceof PageArguments) {
            return $pageArguments->getPageId();
        }
    }

    protected function getFallbackType(int $pageId, int $language)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $rows = $queryBuilder
            ->select('fallbackType')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($pageId, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_language_uid',
                    $queryBuilder->createNamedParameter($language, \PDO::PARAM_INT))
            )
            ->execute()->fetchAll();
        if (isset($rows[0]['fallbackType'])) {
            return $rows[0]['fallbackType'];
        }
    }
}
