import type { ImageMetadata } from 'astro';
import defaultAvatar from '../assets/profile.jpg';

/**
 * Allowed social entry keys in profile configuration.
 */
export type ProfileSocialKey = 'facebook' | 'ig' | 'email' | 'website';

/**
 * One social link item rendered on `/about`.
 */
export interface ProfileSocialLink {
  key: ProfileSocialKey;
  label: string;
  url: string;
}

/**
 * Personal profile settings used by About page and article author schema.
 */
export interface ProfileConfig {
  /**
   * Optional avatar URL for About page and structured data.
   */
  avatar?: string | ImageMetadata;
  /**
   * Display name used across the site.
   */
  name: string;
  /**
   * Short headline/title shown on About page.
   */
  title: string;
  /**
   * Short bio text shown on About page and in schema.
   */
  bio: string;
  /**
   * Optional location text.
   */
  location?: string;
  /**
   * Optional contact email.
   */
  email?: string;
  /**
   * Personal GitHub profile URL (separate from repo URL).
   */
  githubProfileUrl: string;
  /**
   * Social links displayed in About page social row.
   */
  socials: ProfileSocialLink[];
}

export const profileConfig: ProfileConfig = {
  avatar: defaultAvatar,
  name: '喜樂教會',
  title: '凡勞苦擔重擔的人，可以到我這裡來，我就使你們得安息。(馬太福音11:28)',
  bio: '本網站由喜樂教會建立與維護。',
  location: '桃園市',
  email: 'joychurch2000@gmail.com',
  githubProfileUrl: 'https://example.com',
  socials: [
    { key: 'facebook', label: 'Facebook', url: 'https://www.facebook.com/joychurch2000/?locale=zh_TW' },
    { key: 'ig', label: 'Instagram', url: 'https://www.instagram.com/taoyuanjoy2020' },
    { key: 'website', label: 'Website', url: 'https://example.com' },
  ],
};
