<?php
/**
 * Integración con WooCommerce Blocks
 *
 * @package ComunasChile
 */

namespace ComunasChile;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class ComunasChileBlocksIntegration
 */
class ComunasChileBlocksIntegration implements IntegrationInterface {

	/**
	 * Nombre de la integración
	 *
	 * @return string
	 */
	public function get_name() {
		return 'comunas-chile';
	}

	/**
	 * Inicialización
	 *
	 * @return void
	 */
	public function initialize() {
		$this->register_block_scripts();
	}

	/**
	 * Scripts para el editor
	 *
	 * @return array
	 */
	public function get_editor_script_handles() {
		return [];
	}

	/**
	 * Scripts para el frontend
	 *
	 * @return array
	 */
	public function get_script_handles() {
		return [ 'comunas-chile-blocks-frontend' ];
	}

	/**
	 * Datos para scripts
	 *
	 * @return array
	 */
	public function get_script_data() {
		return [
			'countryCode'  => 'CL',
			'stateLabel'   => __( 'Comuna', 'comunas-de-chile-para-woocommerce' ),
			'hidePostcode' => true,
		];
	}

	/**
	 * Registra scripts para bloques
	 *
	 * @return void
	 */
	private function register_block_scripts() {
		$script_content = "
			( function() {
				const { registerCheckoutFilters } = window.wc?.blocksCheckout || {};
				
				if ( typeof registerCheckoutFilters === 'function' ) {
					registerCheckoutFilters( 'comunas-chile', {
						additionalCartCheckoutInnerBlockTypes: ( value, extensions, args ) => {
							return value;
						}
					});
				}
				
				// Modificar label del state cuando el país es Chile
				document.addEventListener( 'DOMContentLoaded', function() {
					const modifyStateLabel = function() {
						const stateLabels = document.querySelectorAll( 'label[for*=\"state\"]' );
						stateLabels.forEach( function( label ) {
							const formContainer = label.closest( '.wc-block-components-address-form' );
							if ( formContainer ) {
								const countryInput = formContainer.querySelector( '[id*=\"country\"] input, select[id*=\"country\"]' );
								if ( countryInput && ( countryInput.value === 'CL' || countryInput.textContent === 'Chile' ) ) {
									if ( label.textContent.includes( 'State' ) || label.textContent.includes( 'Región' ) || label.textContent.includes( 'Region' ) ) {
										label.textContent = 'Comuna';
									}
								}
							}
						});
					};
					
					// MutationObserver para detectar cambios
					const observer = new MutationObserver( modifyStateLabel );
					const checkoutBlock = document.querySelector( '.wc-block-checkout' );
					if ( checkoutBlock ) {
						observer.observe( checkoutBlock, { childList: true, subtree: true, characterData: true } );
					}
					
					setTimeout( modifyStateLabel, 500 );
					setTimeout( modifyStateLabel, 1500 );
				});
			})();
		";

		wp_register_script(
			'comunas-chile-blocks-frontend',
			'',
			[ 'wc-blocks-checkout' ],
			'2026.01.25',
			true
		);

		wp_add_inline_script( 'comunas-chile-blocks-frontend', $script_content );
	}
}
