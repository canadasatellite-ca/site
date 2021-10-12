<?php
namespace Cart2Quote\Features\Feature\ApiAccess;
use Magento\Framework\App\RequestInterface as IRest;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Webapi\Exception as WebapiE;
use Magento\Webapi\Controller\Rest as RestC;
# 2021-10-12 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Cart2Quote_Features` module": https://github.com/canadasatellite-ca/site/issues/250
/** @used-by \Cart2Quote\Features\Feature\ApiAccess::$plugins */
final class Rest extends \Cart2Quote\License\Plugin\AbstractPlugin {
	function __construct() {
		$this->inputParamsResolver = \Magento\Framework\App\ObjectManager::getInstance()->get(
			\Magento\Webapi\Controller\Rest\InputParamsResolver::class
		);
	}

	function aroundDispatch(RestC $restC, callable $f, IRest $YyA0U) {
		$IEgGg = $f($YyA0U);
		$ne57R = $YyA0U->getRequestUri();
		if (!(strpos($ne57R, "\163\145\x72\166\151\143\x65\163\75\141\x6c\154") === false)) {
			goto zOItK;
		}
		try {
			$phYf3 = $this->inputParamsResolver->getRoute()->getServiceClass();
			if (!(
				strpos($phYf3, "\x43\x61\x72\x74\62\x51\x75\157\164\145") !== false
				&& !$this->{"\x69\x73\101\154\154\157\x77\x65\x64"}()
			)) {
				goto qXHh_;
			}
			throw new LE(__(
				"\124\x68\151\163\40\103\x61\162\164\62\x51\165\x6f\x74\x65\x20\x6c\x69\x63\x65\x6e\163\145\40\144\x6f\145\x73\x20\156\157\x74\40\x61\154\x6c\x6f\167\40\x66\x6f\x72\x20\101\120\x49\40\141\x63\x63\145\163\163"
			));
			qXHh_:
		}
		catch (WebapiE $hqo5x) {}
		zOItK:
		return $IEgGg;
	}

	private $inputParamsResolver;
}