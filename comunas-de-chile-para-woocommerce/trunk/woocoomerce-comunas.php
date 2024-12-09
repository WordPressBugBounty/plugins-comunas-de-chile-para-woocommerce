<?php
/*
  Plugin Name: Comunas de Chile para WooCommerce
  Plugin URI: https://andres.reyes.dev
  Description: Activa las Comunas de Chile para WooCommerce y optimiza la experiencia de envío.
  Version: 2024.12.08
  Author: AndresReyesDev <andres@reyes.dev>
  Author URI: https://andres.reyes.dev
  License: GPLv3
  Requires at least: 3.0
  Tested up to: 6.5
  Requires PHP: 7.4
  Requires Plugins: woocommerce 
  WC requires at least: 2.0
  WC tested up to: 9.3.3
 */

namespace ComunasChile;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ComunasChile {
	private static $instance = null;
	private $dismissed_notices = 'chilecourier_notice_dismissed';

	private function __construct() {
		add_action( 'before_woocommerce_init', [ $this, 'declareCompatibility' ] );
		add_filter( 'woocommerce_states', [ $this, 'registerComunas' ] );
		add_filter( 'woocommerce_checkout_fields', [ $this, 'modifyCheckoutFields' ] );
		add_filter( 'woocommerce_get_country_locale', [ $this, 'modifyStateLabel' ] );
		add_action( 'admin_notices', [ $this, 'displayAdminNotice' ] );
		add_action( 'wp_ajax_dismiss_chilecourier_notice', [ $this, 'dismissNotice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminAssets' ] );
	}

	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function declareCompatibility() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}

	public function displayAdminNotice() {
		if ( get_option( $this->dismissed_notices ) ) {
			return;
		}

		$notice = '<div class="notice notice-info is-dismissible chilecourier-notice" style="background: linear-gradient(90deg, #2E2A28 0%, #52525B 100%); border-radius: 8px; padding: 16px; margin: 12px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); color: #FFFFFF;">';
		$notice .= '<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 15px;">';

		// Logo con contenedor
		$notice .= '<div style="flex: 0 0 80px; text-align: center; background: #FFFFFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
		$notice .= '<img src="https://ps.w.org/chilecourier-shipping-for-woocommerce/assets/icon.svg" alt="ChileCourier" style="width: 50px; height: auto;">';
		$notice .= '</div>';

		// Contenido
		$notice .= '<div style="flex: 1; min-width: 200px;">';
		$notice .= '<h3 style="margin: 0 0 8px; font-size: 16px; font-weight: 700; color: #FFB600;">🎉 INVITACIÓN EXCLUSIVA:</h3>';
		$notice .= '<p style="margin: 0 0 8px; font-size: 15px; font-weight: 600; color: #FFFFFF;">🚚 ¡Integra todos los couriers en tu tienda online con un solo clic! <span style="background: #F59E0B; padding: 3px 10px; border-radius: 12px; font-size: 12px; color: #FFFFFF; margin-left: 10px">GRATIS</span></p>';
		$notice .= '<p style="margin: 0; font-size: 14px; line-height: 1.5; color: #E5E7EB;">Starken • Chilexpress • CorreosChile • Bluex • PullmanGo</p>';
		$notice .= '</div>';


		// Botón CTA
		$notice .= '<div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; align-items: center;">';
		$notice .= '<a href="' . admin_url( 'plugin-install.php?s=ChileCourier+Shipping+for+WooCommerce&tab=search&type=term' ) . '" class="button" style="padding: 8px 16px; background: #FFB600; color: #2E2A28; border: none; border-radius: 4px; font-weight: 600; font-size: 13px; text-align: center; width: 220px;">ACTÍVALO GRATIS</a>';
		$notice .= '</div>';

		$notice .= '</div>';
		$notice .= '</div>';

		echo $notice;
	}


	public function enqueueAdminAssets() {
		wp_enqueue_script( 'chilecourier-notice', plugins_url( 'assets/js/admin-notice.js', __FILE__ ), [ 'jquery' ], '1.0', true );
		wp_localize_script( 'chilecourier-notice', 'chileCourierAjax', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'chilecourier_dismiss_notice' )
		] );

		wp_add_inline_script( 'chilecourier-notice', "
            jQuery(document).ready(function($) {
                $(document).on('click', '.chilecourier-notice .notice-dismiss', function() {
                    $.ajax({
                        url: chileCourierAjax.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'dismiss_chilecourier_notice',
                            nonce: chileCourierAjax.nonce
                        }
                    });
                });
            });
        " );
	}

	public function dismissNotice() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'chilecourier_dismiss_notice' ) ) {
			wp_die( 'Invalid nonce' );
		}
		update_option( $this->dismissed_notices, true );
		wp_die();
	}

	public function modifyCheckoutFields( $fields ) {
		$state_modifications = [
			'placeholder' => 'Seleccione una Comuna',
			'label'       => 'Comuna'
		];

		$fields['billing']['billing_state']   = array_merge( $fields['billing']['billing_state'], $state_modifications );
		$fields['shipping']['shipping_state'] = array_merge( $fields['shipping']['shipping_state'], $state_modifications );

		unset( $fields['billing']['billing_postcode'], $fields['shipping']['shipping_postcode'] );

		return $fields;
	}

	public function modifyStateLabel( $locale ) {
		$locale['CL']['state']['label'] = __( 'Comuna', 'woocommerce' );

		return $locale;
	}

	public function registerComunas( $states ) {
		include __DIR__ . '/data/communes.php';

		return $states;
	}
}

ComunasChile::getInstance();