<?php

require "Mage/Checkout/controllers/CartController.php";

class Enjoy_AjaxAddToCart_CartController extends Mage_Checkout_CartController
{
	
	

    /**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {
        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }
		
		echo Mage::getSingleton('checkout/cart')->getSummaryQty();
    }

	public function addAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_goBack();
            return;
        }
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
            }
            echo Mage::getSingleton('checkout/cart')->getSummaryQty();
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            echo Mage::getSingleton('checkout/cart')->getSummaryQty();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            echo 0;
        }
    }

	public function supportAction(){
		//$this->loadLayout();
		if($this->getRequest()->getPost()){
			$data = $this->getRequest()->getPost();
			if(count($data)>5){
				$subject = $data['subject'];
				$name = $data['name'];
				$email = $data['email'];
				$orderId = $data['order_id'];
				$description = $data['description'];
				$phone = $data['phone'];
				try{
					$mail = new Zend_Mail();
					$mail->addTo('Support Team', 'support@peep.vn');
					$mail->setFrom($name,$email);
					$mail->setSubject($subject);
					
					$body = '<table><tbody>';
					$body .= '<tr><td>Tên: </td><td>'.$name.'</td></tr>';				
					$body .= '<tr><td>Email: </td><td>'.$email.'</td></tr>';				
					$body .= '<tr><td>Chi tiết: </td><td>'.$description.'</td></tr>';		
					$body .= '<tr><td>Số đơn hàng: </td><td>'.$oder_id.'</td></tr>';		
					$body .= '<tr><td>Số điện thoại: </td><td>'.$phone.'</td></tr>';
					$body .= '</tbody></table>';
					
					$mail->setBodyHtml($body);
					@$mail->send();
					echo '1';
				}catch(Exception $e){
					echo 'failed';
					echo $e->getMessage();
				}
			}
		}
		
		//$this->renderLayout();
	}
}