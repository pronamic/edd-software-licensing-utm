<?php
/*
Plugin Name: Easy Digital Downloads - Software Licensing - UTM
Plugin URI: https://www.pronamic.eu/plugins/edd-software-licensing-utm/
Description: Extend Easy Digital Downloads - Software Licensing plugin with UTM (Urchin Tracking Module) parameters support.

Version: 1.0.0
Requires at least: 4.7

Author: Pronamic
Author URI: https://www.pronamic.eu/

Text Domain: pronamic-edd-sl-utm
Domain Path: /languages/

License: GPL

GitHub URI: https://github.com/pronamic/edd-software-licensing-utm
*/

class PronamicEasyDigitalDownloadsSoftwareLicensingUtmPlugin {
	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Construct.
	 *
	 * @param string $file Plugin file.
	 */
	public function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Setup.
	 */
	public function setup() {
		add_action( 'init', array( $this, 'init' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		add_filter( 'edd_sl_get_renewal_url', array( $this, 'extend_edd_sl_renewal_url_with_utm' ), 10, 3 );
		add_filter( 'edd_get_checkout_uri', array( $this, 'extend_edd_checkout_uri_with_utm' ) );
	}

	/**
	 * Initialize.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_setting/
	 * @link https://ga-dev-tools.appspot.com/campaign-url-builder/
	 */
	public function init() {
		register_setting( 'general', 'pronamic_edd_sl_renewal_url_utm_source', array(
			'type'              => 'string',
			'description'       => __( 'Use `utm_source` to identify a search engine, newsletter name, or other source.', 'pronamic-edd-sl-utm' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		register_setting( 'general', 'pronamic_edd_sl_renewal_url_utm_medium', array(
			'type'              => 'string',
			'description'       => __( 'Use `utm_source` to identify a search engine, newsletter name, or other source.', 'pronamic-edd-sl-utm' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		register_setting( 'general', 'pronamic_edd_sl_renewal_url_utm_campaign', array(
			'type'              => 'string',
			'description'       => __( 'Used for keyword analysis. Use `utm_campaign` to identify a specific product promotion or strategic campaign.', 'pronamic-edd-sl-utm' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		register_setting( 'general', 'pronamic_edd_sl_renewal_url_utm_term', array(
			'type'              => 'string',
			'description'       => __( 'Used for paid search. Use `utm_term` to note the keywords for this ad.', 'pronamic-edd-sl-utm' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );

		register_setting( 'general', 'pronamic_edd_sl_renewal_url_utm_content', array(
			'type'              => 'string',
			'description'       => __( 'Used for A/B testing and content-targeted ads. Use `utm_content` to differentiate ads or links that point to the same URL.', 'pronamic-edd-sl-utm' ),
			'sanitize_callback' => 'sanitize_text_field',
		) );
	}

	/**
	 * Admin initialize.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_settings_section/
	 * @link https://developer.wordpress.org/reference/functions/add_settings_field/
	 */
	public function admin_init() {
		add_settings_section(
			'pronamic_edd_sl_renewal_url_utm',
			__( 'Easy Digital Downloads - Software Licensing - Renewal URL - UTM', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_section_callback' ),
			'general'
		);

		add_settings_field(
			'pronamic_edd_sl_renewal_url_utm_source',
			__( 'Source', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_field_callback' ),
			'general',
			'pronamic_edd_sl_renewal_url_utm',
			array(
				'label_for' => 'pronamic_edd_sl_renewal_url_utm_source',
			)
		);

		add_settings_field(
			'pronamic_edd_sl_renewal_url_utm_medium',
			__( 'Medium', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_field_callback' ),
			'general',
			'pronamic_edd_sl_renewal_url_utm',
			array(
				'label_for' => 'pronamic_edd_sl_renewal_url_utm_medium',
			)
		);

		add_settings_field(
			'pronamic_edd_sl_renewal_url_utm_campaign',
			__( 'Campaign', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_field_callback' ),
			'general',
			'pronamic_edd_sl_renewal_url_utm',
			array(
				'label_for' => 'pronamic_edd_sl_renewal_url_utm_campaign',
			)
		);

		add_settings_field(
			'pronamic_edd_sl_renewal_url_utm_term',
			__( 'Term', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_field_callback' ),
			'general',
			'pronamic_edd_sl_renewal_url_utm',
			array(
				'label_for' => 'pronamic_edd_sl_renewal_url_utm_term',
			)
		);

		add_settings_field(
			'pronamic_edd_sl_renewal_url_utm_content',
			__( 'Content', 'pronamic-edd-sl-utm' ),
			array( $this, 'settings_field_callback' ),
			'general',
			'pronamic_edd_sl_renewal_url_utm',
			array(
				'label_for' => 'pronamic_edd_sl_renewal_url_utm_content',
			)
		);
	}

	/**
	 * Settings section.
	 */
	public function settings_section_callback() {
		$url = 'https://ga-dev-tools.appspot.com/campaign-url-builder/';

		$codes = array(
			'{name}' => __( 'The name of the license \'title\'.', 'pronamic-edd-sl-utm' ),
		);

		?>
		<p>
			<?php

			printf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html( $url )
			);

			?>
		</p>

		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Variable code', 'pronamic-edd-sl-utm' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Variable description', 'pronamic-edd-sl-utm' ); ?></th>
				</tr>
			</thead>

			<tbody>
				
				<?php foreach ( $codes as $code => $description ) : ?>

					<tr>
						<td><?php echo esc_html( $code ); ?></td>
						<td><?php echo esc_html( $description ); ?></td>
					</tr>

				<?php endforeach; ?>

			</tbody>
		</table>
		<?php
	}

	/**
	 * Settings field callback.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.1/wp-includes/option.php#L2242-L2259
	 *
	 * @param array $args Arguments.
	 */
	public function settings_field_callback( $args ) {
		$name  = $args['label_for'];
		$id    = $args['label_for'];
		$value = get_option( $name );

		printf(
			'<input name="%s" type="text" id="%s" value="%s" class="regular-text" />',
			esc_attr( $name ),
			esc_attr( $id ),
			esc_attr( $value )
		);

		$registered_settings = get_registered_settings();

		if ( array_key_exists( $id, $registered_settings ) ) {
			$registered_setting = $registered_settings[ $id ];

			if ( array_key_exists( 'description', $registered_setting ) ) {
				$description = $registered_setting['description'];

				printf(
					'<p class="description">%s</p>',
					esc_html( $description )
				);
			}
		}
	}

	/**
	 * Format UTM parameter.
	 *
	 * @link https://www.php.net/manual/en/function.strtr.php
	 * @link https://github.com/woocommerce/woocommerce/blob/3.5.7/includes/emails/class-wc-email.php#L270-L308
	 *
	 * @param string $value Value to format.
	 * @param array  $replace_pairs Replace pairs.
	 * @return string
	 */
	private function format_utm_parameter( $value, array $replace_pairs = array() ) {
		$value = strtr( $value, $replace_pairs );

		return $value;
	}

	/**
	 * Get UTM parameter by option name.
	 *
	 * @param string $option        Option name.
	 * @param array  $replace_pairs Replace pairs.
	 * @return string
	 */
	private function get_utm_parameter( $option, array $replace_pairs = array() ) {
		$value = get_option( $option );

		$value = $this->format_utm_parameter( $value, $replace_pairs );

		return $value;
	}

	/**
	 * Extend Easy Digital Downloads - Software Licensing - Renewal URL with UTM parameters.
	 *
	 * @link https://github.com/wp-premium/edd-software-licensing/blob/3.6.8/includes/classes/class-sl-license.php#L1209-L1224
	 * @link https://github.com/wp-premium/edd-software-licensing/blob/3.6.8/includes/classes/class-sl-license.php#L663-L682
	 *
	 * @param string         $url        Renewal URL.
	 * @param int            $license_id License ID.
	 * @param EDD_SL_License $license    License object.
	 * @return string
	 */
	public function extend_edd_sl_renewal_url_with_utm( $url, $license_id, $license ) {
		$replace_pairs = array(
			'{name}' => $license->get_name(),
		);

		$map = 	array(
			'utm_source'   => 'pronamic_edd_sl_renewal_url_utm_source',
			'utm_medium'   => 'pronamic_edd_sl_renewal_url_utm_medium',
			'utm_campaign' => 'pronamic_edd_sl_renewal_url_utm_campaign',
			'utm_term'     => 'pronamic_edd_sl_renewal_url_utm_term',
			'utm_content'  => 'pronamic_edd_sl_renewal_url_utm_content',
		);

		$parameters = array();

		foreach ( $map as $parameter => $option ) {
			$parameters[ $parameter ] = $this->get_utm_parameter( $option, $replace_pairs );
		}

		$parameters = array_filter( $parameters );

		if ( empty( $parameters ) ) {
			return $url;
		}
		
		$parameters = urlencode_deep( $parameters );

		$url = add_query_arg( $parameters, $url );

		return $url;
	}

	/**
	 * Extend Easy Digital Downloads - Checkout page URI with UTM parameters.
	 *
	 * @link https://github.com/wp-premium/edd-software-licensing/blob/3.6.8/includes/license-renewals.php#L186-L213
	 *
	 * @param string $uri Checkout page URI.
	 * @return string
	 */
	public function extend_edd_checkout_uri_with_utm( $uri ) {
		$parameters = filter_input_array( INPUT_GET, array(
			'utm_source'   => FILTER_SANITIZE_STRING,
			'utm_medium'   => FILTER_SANITIZE_STRING,
			'utm_campaign' => FILTER_SANITIZE_STRING,
			'utm_term'     => FILTER_SANITIZE_STRING,
			'utm_content'  => FILTER_SANITIZE_STRING,
		) );

		if ( empty( $parameters ) ) {
			return $uri;
		}

		$parameters = array_filter( $parameters );

		if ( empty( $parameters ) ) {
			return $uri;
		}

		$parameters = urlencode_deep( $parameters );

		$uri = add_query_arg( $parameters, $uri );

		return $uri;
	}
}

$pronamic_edd_sl_utm_plugin = new PronamicEasyDigitalDownloadsSoftwareLicensingUtmPlugin( __FILE__ );

$pronamic_edd_sl_utm_plugin->setup();
