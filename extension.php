<?php

declare(strict_types=1);

final class CustomStylesheetExtension extends Minz_Extension {
    protected array $csp_policies = [];
    
	/**
     * Stylesheet URL
     */
    private string $stylesheet_url = '';

	#[\Override]
	public function init(): void {
        $this->registerTranslates();

        $url = $this->getStylesheetUrl() ?: FreshRSS_Context::userConf()->attributeString('stylesheet_url');
        if ($url && parse_url($url)) {
            $this->csp_policies['style-src'] = "'self' {$url}";
        }

		Minz_View::prependStyle($url);
	}

	/**
     * Initializes the extension configuration, if the user context is available.
     */
    public function loadConfigValues(): void
    {
        if (!class_exists('FreshRSS_Context', false) || !FreshRSS_Context::hasUserConf()) {
            return;
        }

		$stylesheet_url = FreshRSS_Context::userConf()->attributeString('stylesheet_url');
        if ($stylesheet_url !== null) {
            $this->stylesheet_url = $stylesheet_url;
        }
    }

	/**
	 * Returns the stylesheet URL
	 */
    public function getStylesheetUrl(): string
    {
        return $this->stylesheet_url;
    }

	/**
     * This function is called by FreshRSS when the configuration page is loaded, and when configuration is saved.
     *  - We save configuration in case of a post.
     *  - We (re)load configuration in all case, so they are in-sync after a save and before a page load.
     */
	#[\Override]
    public function handleConfigureAction(): void
    {
        $this->registerTranslates();

        if (Minz_Request::isPost()) {
            FreshRSS_Context::userConf()->_attribute('stylesheet_url', Minz_Request::paramString('stylesheet_url'));
            FreshRSS_Context::userConf()->save();
        }

        $this->loadConfigValues();
    }
}
