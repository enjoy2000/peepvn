<?php

require "Mage/Checkout/controllers/CartController.php";

class Enjoy_AjaxAddToCart_CartController extends Mage_Checkout_CartController
{

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
		$this->loadLayout();
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
					Mage::getSingleton('core/session')->addSuccess('Yêu cầu của bạn đã được gửi đi.');
				}catch(Exception $e){
					Mage::getSingleton('core/session')->addError($e->getMessage());
				}
			}
		}
		
		$this->renderLayout();
	}
}