/**
 * Site-level settings shared by header, SEO tags, and feed generation.
 */
export interface SiteConfig {
  /**
   * Canonical production URL of this site.
   */
  siteUrl: string;
  /**
   * Global site title used in header and metadata.
   */
  siteTitle: string;
  /**
   * Optional suffix appended to browser/SEO page titles.
   */
  siteTitleSuffix: string;
  /**
   * Default site description used by index and RSS metadata.
   */
  siteDescription: string;
  /**
   * BCP-47 locale tag (for example: zh-TW, en-US).
   */
  locale: string;
  /**
   * Repository URL shown in the header action area.
   */
  headerGithubRepoUrl: string;
  /**
   * Global favicon ico path served from the public directory.
   */
  faviconIco: string;
}

export const siteConfig: SiteConfig = {
  siteUrl: 'https://template.ulna520.top',
  siteTitle: '門訓資源網',
  siteTitleSuffix: '成為門徒，使人作門徒',
  siteDescription: '門訓資源網是為了幫助基督徒成為耶穌的門徒，並且去使人作門徒而設立的資源平台。我們提供各種門訓資源，包括文章、視頻、課程和社群活動，旨在幫助信徒更深入地了解聖經教導，並在日常生活中實踐信仰。無論你是新信徒還是成熟的基督徒，我們都希望這個平台能夠成為你靈命成長的幫助。',
  locale: 'zh-TW',
  headerGithubRepoUrl: 'https://github.com/xxy1103/ulbo-astro-theme-template',
  faviconIco: '/favicon.ico',
};

export const { siteUrl, siteTitle, siteTitleSuffix, siteDescription, locale, headerGithubRepoUrl, faviconIco } = siteConfig;
