<?php
/**
 * Ollie Ford & Co Social Connect.
 *
 *
 * @package   Ollie Ford & Co Social Connect
 * @author    Rubén Madila (for Ollie Ford & Co) <ruben@ollieford.co.uk>
 * @license   GPL-2.0+
 * @link      http://www.ollieford.co.uk
 * @copyright 2014 Ollie Ford & Co
 */

/**
 * Standarising Social assets for the Social Wall
 */
class OF_Social_Asset {

	public $type = null;
	public $main_image_url;
	public $color = '#333';
	public $title = null;
	public $asset_type = null;
	public $asset_action = null;
	public $user_id = null;
	public $asset_id = null;
	public $full_name = null;
	public $asset_url = null;
	public $main_image_meta = null;
	public $main_content = null;
	public $main_image_caption = null;
	public $thumbnail_url = null;
	public $retweet_id = null;
	public $thumbnail_meta = null;
	public $is_image = false;
	public $username = null;
	public $avatar_url = null;
	public $display_date = null;
	public $date = null;
	protected $asset = null;

	function __construct() {

	}

	public function return_normalized_asset( $asset = null, $type = 'any' ) {
		$this->asset = $asset;
		if ( is_null( $this->asset ) || ! is_object( $this->asset ) ) {
			$this->asset = new \stdClass;
		}
		$this->type = $type;
		$this->process_asset();

		return $this;
	}

	function process_asset() {
		switch ( $this->type ) {
			case 'instagram':
				$this->process_instagram();
				break;
			case 'twitter':
				$this->process_twitter();
				break;
			case 'facebook':
				$this->process_facebook();
				break;
			default:
				break;
		}

	}

	function process_instagram() {
		$this->color = '#d93175';
		// Instagram Asset Type
		if ( property_exists( $this->asset, 'type' ) ) {
			$this->asset_type = $this->asset->type;
			if ( $this->asset_type == 'image' ) {
				$this->is_image = true;
			}
		}
		// Instagram Asset URL
		if ( property_exists( $this->asset, 'link' ) ) {
			$this->asset_url = $this->asset->link;
		}
		// Main Image
		if ( property_exists( $this->asset, 'images' ) ) {
			$image                 = $this->asset->images;
			$this->main_image_url  = $image->standard_resolution->url;
			$this->main_image_meta = array(
				'width'  => $image->standard_resolution->width,
				'height' => $image->standard_resolution->height
			);
			$this->thumbnail_url   = $image->thumbnail->url;
			$this->thumbnail_meta  = array(
				'width'  => $image->thumbnail->width,
				'height' => $image->thumbnail->height
			);
		}
		// User
		if ( property_exists( $this->asset, 'user' ) ) {
			$user             = $this->asset->user;
			$this->avatar_url = $user->profile_picture;
			$this->username   = $user->username;
			$this->full_name  = $user->full_name;
			$this->user_id    = $user->id;
		}
		// Main Content
		if ( property_exists( $this->asset, 'caption' ) ) {
			$caption                  = $this->asset->caption;
			$this->main_image_caption = $caption->text;
			$this->avatar_url         = $caption->from->profile_picture;
			$this->date               = $caption->created_time;
		}
		$this->asset = null;
	}

	function process_twitter() {
		$this->color = '#1da1f2';

		if ( property_exists( $this->asset, 'retweeted_status' ) ) {
			$this->retweet_id = $this->asset->retweeted_status->id;
		}

		// Main Image
		if ( property_exists( $this->asset, 'entities' ) ) {
			if ( property_exists( $this->asset->entities, 'media' ) ) {
				$images = $this->asset->entities->media;
				if ( count( $images ) > 0 ) {
					$main_image = $images[0];
					if ( $main_image->type == 'photo' ) {
						$this->main_image_url  = $main_image->media_url_https;
						$this->main_image_meta = array(
							'width'  => $main_image->sizes->medium->w,
							'height' => $main_image->sizes->medium->h
						);
					}
				}
			}
		}
		if ( property_exists( $this->asset, 'created_at' ) ) {
			$this->date = strtotime( $this->asset->created_at );
		}
		// User
		if ( property_exists( $this->asset, 'user' ) ) {
			$user             = $this->asset->user;
			$this->avatar_url = $user->profile_picture;
			$this->username   = $user->screen_name;
			$this->full_name  = $user->name;
			$this->user_id    = $user->id;
			$this->avatar_url = $user->profile_image_url;
			$this->asset_url  = 'https://twitter.com/' . $user->screen_name . '/status/' . $this->asset->id;
		}
		// Main Content
		if ( property_exists( $this->asset, 'text' ) ) {
			$this->main_content = $this->asset->text;
		}
		// Main Content
		if ( property_exists( $this->asset, 'full_text' ) ) {
			$this->main_content = $this->asset->full_text;
		}
		$this->asset = null;
	}

	function process_facebook() {
		if ( property_exists( $this->asset, 'is_hidden' ) ) {

			if ( ! $this->asset->is_hidden ) {
				$this->color = '#4267b2';
				// Main Image
				if ( property_exists( $this->asset, 'full_picture' ) ) {
					$this->main_image_url = $this->asset->full_picture;
				}
				if ( property_exists( $this->asset, 'type' ) ) {
					$this->asset_type = $this->asset->type;
				}
				// Date
				if ( property_exists( $this->asset, 'created_time' ) ) {
					$this->date = strtotime( $this->asset->created_time );
				}
				// ID
				if ( property_exists( $this->asset, 'id' ) ) {
					$this->asset_id = $this->asset->id;
				}
				// Main Content
				if ( property_exists( $this->asset, 'message' ) ) {
					$this->main_content = $this->asset->message;
				}

				if ( property_exists( $this->asset, 'name' ) ) {
					$this->title = $this->asset->name;
				}

				if ( property_exists( $this->asset, 'story' ) ) {
					$this->asset_action = $this->asset->story;
				}

				if ( property_exists( $this->asset, 'caption' ) ) {
					$this->main_image_caption = $this->asset->caption;
				}

				if ( property_exists( $this->asset, 'permalink_url' ) ) {
					$this->asset_url = $this->asset->permalink_url;
				}

				$this->asset = null;
			}

		}

	}

	function has_content() {
		return ( ! empty( $this->main_image_url ) || ! empty( $this->main_content ) );
	}

	function get_icon() {
		return '<span class="social-wall-badge" style="background-color:' . $this->color . '"><i class="fa fa-' . $this->get_type() . '"></i></span>';
	}

	function get_type() {
		return $this->type;
	}

	function process_text( $text ) {
		return wpautop( $this->process_hashtags( $this->process_links($text) ) );
	}

	function process_hashtags( $text ) {
		$url = ( $this->type == 'instagram' ) ? 'https://www.instagram.com/explore/tags/' : 'https://twitter.com/hashtag/';

		return preg_replace( '/(?<!\S)#([0-9a-zA-Z]+)/', '<a target="_blank" rel="nofollow" href="' . $url . '$1">#$1</a>', $text );
	}

	function process_links( $text ) {
		// The Regular Expression filter
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		// Check if there is a url in the text
		if ( preg_match( $reg_exUrl, $text, $url ) ) {

			// make the urls hyper links
			return preg_replace( $reg_exUrl, "<a href='{$url[0]}' target='_blank'>{$url[0]}</a> ", $text);

		} else {

			// if no urls in the text just return the text
			return $text;

		}
	}

}
