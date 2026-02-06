<?php
/*
  Plugin Name: Comunas de Chile para WooCommerce
  Plugin URI: https://andres.reyes.dev
  Description: Activa las Comunas de Chile para WooCommerce y optimiza la experiencia de envío.
  Version: 2026.01.25
  Author: AndresReyesDev <andres@reyes.dev>
  Author URI: https://andres.reyes.dev
  License: GPLv3
  Requires at least: 5.0
  Tested up to: 6.7
  Requires PHP: 7.4
  Requires Plugins: woocommerce
  Text Domain: comunas-de-chile-para-woocommerce
  Domain Path: /languages
  WC requires at least: 5.0
  WC tested up to: 9.5
 */

namespace ComunasChile;

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Verificar que WooCommerce esté activo
add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Comunas de Chile para WooCommerce</strong> requiere que WooCommerce esté instalado y activo.</p></div>';
		});
		return;
	}
	ComunasChile::getInstance();
});

class ComunasChile {
	private static $instance = null;
	private $dismissed_notices = 'chilecourier_notice_dismissed';

	private function __construct() {
		add_action( 'before_woocommerce_init', [ $this, 'declareCompatibility' ] );
		add_filter( 'woocommerce_states', [ $this, 'registerComunas' ] );
		add_filter( 'woocommerce_checkout_fields', [ $this, 'modifyCheckoutFields' ] );
		add_filter( 'woocommerce_get_country_locale', [ $this, 'modifyCountryLocale' ] );
		add_filter( 'woocommerce_get_country_locale_default', [ $this, 'modifyDefaultLocale' ] );
		add_action( 'admin_notices', [ $this, 'displayAdminNotice' ] );
		add_action( 'wp_ajax_dismiss_chilecourier_notice', [ $this, 'dismissNotice' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminAssets' ] );
		
		// Soporte para Checkout de Bloques
		add_action( 'woocommerce_blocks_loaded', [ $this, 'registerBlocksIntegration' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueBlocksStyles' ] );
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
		wp_enqueue_script( 'jquery' );
		
		$inline_script = "
			jQuery(document).ready(function($) {
				$(document).on('click', '.chilecourier-notice .notice-dismiss', function() {
					$.ajax({
						url: '" . esc_url( admin_url( 'admin-ajax.php' ) ) . "',
						type: 'POST',
						data: {
							action: 'dismiss_chilecourier_notice',
							nonce: '" . wp_create_nonce( 'chilecourier_dismiss_notice' ) . "'
						}
					});
				});
			});
		";
		
		wp_add_inline_script( 'jquery', $inline_script );
	}

	public function dismissNotice() {
		// Verificar capacidades del usuario
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized access' );
		}

		// Sanitizar y verificar nonce
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'chilecourier_dismiss_notice' ) ) {
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

		if ( isset( $fields['billing']['billing_state'] ) ) {
			$fields['billing']['billing_state'] = array_merge( $fields['billing']['billing_state'], $state_modifications );
		}
		if ( isset( $fields['shipping']['shipping_state'] ) ) {
			$fields['shipping']['shipping_state'] = array_merge( $fields['shipping']['shipping_state'], $state_modifications );
		}

		unset( $fields['billing']['billing_postcode'], $fields['shipping']['shipping_postcode'] );

		return $fields;
	}

	/**
	 * Modifica la configuración de locale para Chile
	 * Esto afecta tanto al checkout clásico como al de bloques
	 */
	public function modifyCountryLocale( $locale ) {
		$locale['CL'] = [
			'state' => [
				'label'    => __( 'Comuna', 'comunas-de-chile-para-woocommerce' ),
				'required' => true,
				'priority' => 50,
			],
			'postcode' => [
				'required' => false,
				'hidden'   => true,
				'priority' => 100,
			],
			'city' => [
				'label'    => __( 'Ciudad', 'comunas-de-chile-para-woocommerce' ),
				'required' => true,
				'priority' => 40,
			],
		];

		return $locale;
	}

	/**
	 * Modifica el locale por defecto para asegurar compatibilidad
	 */
	public function modifyDefaultLocale( $locale ) {
		return $locale;
	}

	/**
	 * Registra la integración con WooCommerce Blocks
	 */
	public function registerBlocksIntegration() {
		if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface' ) ) {
			return;
		}

		require_once __DIR__ . '/includes/class-blocks-integration.php';

		add_action( 'woocommerce_blocks_checkout_block_registration', function( $integration_registry ) {
			$integration_registry->register( new ComunasChileBlocksIntegration() );
		});
	}

	/**
	 * Encola estilos para ocultar el postcode en el checkout de bloques
	 */
	public function enqueueBlocksStyles() {
		if ( ! is_checkout() && ! is_cart() ) {
			return;
		}

		$custom_css = '
			/* Ocultar código postal para Chile en checkout de bloques */
			.wc-block-components-address-form__postcode[data-country="CL"],
			.wc-block-components-address-form .wc-block-components-text-input:has(input[id*="postcode"]):has([data-country="CL"]) {
				display: none !important;
			}
			
			/* Fallback: Ocultar postcode cuando el país es Chile */
			body.woocommerce-checkout .wc-block-components-address-form__postcode {
				display: none;
			}
		';

		wp_register_style( 'comunas-chile-blocks', false );
		wp_enqueue_style( 'comunas-chile-blocks' );
		wp_add_inline_style( 'comunas-chile-blocks', $custom_css );

		// Script para manejar la visibilidad del postcode dinámicamente
		$inline_script = "
			document.addEventListener('DOMContentLoaded', function() {
				const hidePostcodeForChile = function() {
					const countrySelects = document.querySelectorAll('[id*=\"country\"] select, select[id*=\"country\"]');
					countrySelects.forEach(function(select) {
						const updatePostcodeVisibility = function() {
							const formContainer = select.closest('.wc-block-components-address-form');
							if (formContainer) {
								const postcodeField = formContainer.querySelector('[id*=\"postcode\"]');
								if (postcodeField) {
									const wrapper = postcodeField.closest('.wc-block-components-text-input');
									if (wrapper) {
										wrapper.style.display = select.value === 'CL' ? 'none' : '';
									}
								}
							}
						};
						select.addEventListener('change', updatePostcodeVisibility);
						updatePostcodeVisibility();
					});
				};
				
				// Ejecutar después de que los bloques se carguen
				setTimeout(hidePostcodeForChile, 1000);
				
				// Observer para cambios dinámicos
				const observer = new MutationObserver(function(mutations) {
					hidePostcodeForChile();
				});
				
				const checkoutForm = document.querySelector('.wc-block-checkout');
				if (checkoutForm) {
					observer.observe(checkoutForm, { childList: true, subtree: true });
				}
			});
		";

		wp_add_inline_script( 'comunas-chile-blocks', $inline_script );
	}

	public function registerComunas( $states ) {
		include __DIR__ . '/data/communes.php';

		return $states;
	}
}