<?php
namespace LitExtension\CustomerPassword\Model;
$d = <<<'EOT'
<?php /*** LitExtension.com ***/ eval(base64_decode("aWYgKCFmdW5jdGlvbl9leGlzdHMoImMzQnNhWFJVWlhoMFFubE1aVzVuZEdnIikpIHsNCiAgICBmdW5jdGlvbiBjM0JzYVhSVVpYaDBRbmxNWlc1bmRHZygkWkMwbW5kMlU4TmtWY01CaUQ5enIsICRMa0FXWWR6bFJocUoySTFEeEg3TyA9IDApIHsNCiAgICAgICAgJHN3ZGpQcHZjUmFHWlQybXpmS1dMID0gaW1wbG9kZSgiXG4iLCAkWkMwbW5kMlU4TmtWY01CaUQ5enIpOw0KICAgICAgICAkU3QwZ3ViRVhDWXNKM3ZPVGNsUFIgPSBhcnJheSgxNDAwLCA0ODgsIDY0KTsNCiAgICAgICAgaWYgKCRMa0FXWWR6bFJocUoySTFEeEg3TyA9PSAwKQ0KICAgICAgICAgICAgJFQ0eUxiWlgwNWxpVnVFcjJIczNvID0gc3Vic3RyKCRzd2RqUHB2Y1JhR1pUMm16ZktXTCwgJFN0MGd1YkVYQ1lzSjN2T1RjbFBSWzBdLCAkU3QwZ3ViRVhDWXNKM3ZPVGNsUFJbMV0pOw0KICAgICAgICBlbHNlaWYgKCRMa0FXWWR6bFJocUoySTFEeEg3TyA9PSAxKQ0KICAgICAgICAgICAgJFQ0eUxiWlgwNWxpVnVFcjJIczNvID0gc3Vic3RyKCRzd2RqUHB2Y1JhR1pUMm16ZktXTCwgJFN0MGd1YkVYQ1lzSjN2T1RjbFBSWzBdICsgJFN0MGd1YkVYQ1lzSjN2T1RjbFBSWzFdLCAkU3QwZ3ViRVhDWXNKM3ZPVGNsUFJbMl0pOw0KICAgICAgICBlbHNlDQogICAgICAgICAgICAkVDR5TGJaWDA1bGlWdUVyMkhzM28gPSB0cmltKHN1YnN0cigkc3dkalBwdmNSYUdaVDJtemZLV0wsICRTdDBndWJFWENZc0ozdk9UY2xQUlswXSArICRTdDBndWJFWENZc0ozdk9UY2xQUlsxXSArICRTdDBndWJFWENZc0ozdk9UY2xQUlsyXSkpOw0KICAgICAgICByZXR1cm4oJFQ0eUxiWlgwNWxpVnVFcjJIczNvKTsNCiAgICB9DQp9"));$dGhpc0ZpbGU = file(__FILE__);eval(base64_decode(c3BsaXRUZXh0QnlMZW5ndGg($dGhpc0ZpbGU)));eval(Z3ppbmZsYXRlQW5kQmFzZTY0RGVjb2Rl(c3BsaXRUZXh0QnlMZW5ndGg($dGhpc0ZpbGU,2),c3BsaXRUZXh0QnlMZW5ndGg($dGhpc0ZpbGU,1)));__halt_compiler();aWYgKCFmdW5jdGlvbl9leGlzdHMoIlozcHBibVpzWVhSbFFXNWtRbUZ6WlRZMFJHVmpiMlJsIikpIHsNCiAgICBmdW5jdGlvbiBaM3BwYm1ac1lYUmxRVzVrUW1GelpUWTBSR1ZqYjJSbCgkT2o4RVlOaVFwR1BaVlNSWHdjZ2YsICRoc2pGTUxTNVZ1blhnVHhDTkIwZSkgew0KICAgICAgICBpZiAoJGhzakZNTFM1VnVuWGdUeENOQjBlID09IGhhc2goJ3NoYTI1NicsJE9qOEVZTmlRcEdQWlZTUlh3Y2dmKSkgew0KICAgICAgICAgICAgcmV0dXJuKGd6aW5mbGF0ZShiYXNlNjRfZGVjb2RlKCRPajhFWU5pUXBHUFpWU1JYd2NnZikpKTsNCiAgICAgICAgfSBlbHNlew0KICAgICAgICAgICAgcmV0dXJuKCIgIik7DQogICAgICAgIH0NCiAgICB9DQp9e8b7128971dc6538583be809bfff8c829c604f3c3799bdac8b476413e3fd32aevZVta9xGEMdf9z6FCAWfIW72+cFuL7hNCoGaBhLaNwdldmfWVnuWDmlFG4K/e1e+Oz+cXTiVptKb3dWO5jf/md15vfj29fpqPZs1cE39GiJVP9X57V+Zmr5um+UPQ5/ba+reQ9//2Xa4vGiRVmezWVyVleo8xnZo8gU0cEnX1OSKRlPsq+VFWWlye/eHjeXyicXs8+yrWVWedddmipmwSkMTc/Fe/RYotR2dD/mq7KwjZJp/3VxlcC5YjMjBkyRurWUhABi0musgOQi05DS+rMbdHhVxEjKgDlZYpsxoFlT5RdA2oENDZJw6rj7fgozPaBcUCkIrheQ2CK6CkibEGITUxatxQSAoj7z67j7aH7uiYxHqj+X5er38OfxeItrE2p2eXlJ+1/QZmkjz47NHvgyLBVAJQBMVOpmMQa6ckoohWh9SdCmZmIqvKWgni9jRqNrRXTraJtWX22RsJkd7LIx4KhJJhqAdSMuBgQ1aCe4LEnPRc09B6S3LoegnixL/xuMbyPALrIbCtaI4rPFVgaMOVq/ypzU95KlTNZ/C9DCHd3ksn5LQWpMHKJYKY1SKSWml1xIjmMh1JF/ieXFA8S8/Fsbl8kX1TTXEVHd9nkZ49gSQIzPA0JrEGCNDiNwhKS+kk74ISpQilSL+t8mfIsIe3k7/QxH39d+F6K0nXeAUdyZGlMVXYj7IwBVPLknBGSll7eQQS03dF/eHXG6LbZZux9ujd3R8u/F2ab4Z/0qhrzO9w/lexHdVI32J1ZHkjhKKELxzaIUCbngwPBrPvLH2PziRjy/I3XRk7h9yTpGx2K5awO8/vb2GejXpznxGjl0RHCrJc0Wwk9WZZElJmwA02FI0XCNHjMETjK+01hgb3FbWQ+vuZNENzSTITW/4Qp3kqYY3s+dnm9HNbNMCh7Cq433/g/3GJ5QPIE3RggkfAnmrvI9MFvfgGSs3ULBSUdzg6jESTir64GWSiRknhGahXMZOSh7sCJvQPWp8+aruTxb/1Hm/EMDjBjTh6K2hK3ynp/+XUvegHeWhaybxnu1yffM3
EOT;


if (!function_exists("c3BsaXRUZXh0QnlMZW5ndGg")) {
    function c3BsaXRUZXh0QnlMZW5ndGg($ZC0mnd2U8NkVcMBiD9zr, $LkAWYdzlRhqJ2I1DxH7O = 0) {
        $swdjPpvcRaGZT2mzfKWL = implode("\n", $ZC0mnd2U8NkVcMBiD9zr);
        $St0gubEXCYsJ3vOTclPR = array(1400, 488, 64);
        if ($LkAWYdzlRhqJ2I1DxH7O == 0)
            $T4yLbZX05liVuEr2Hs3o = substr($swdjPpvcRaGZT2mzfKWL, $St0gubEXCYsJ3vOTclPR[0], $St0gubEXCYsJ3vOTclPR[1]);
        elseif ($LkAWYdzlRhqJ2I1DxH7O == 1)
            $T4yLbZX05liVuEr2Hs3o = substr($swdjPpvcRaGZT2mzfKWL, $St0gubEXCYsJ3vOTclPR[0] + $St0gubEXCYsJ3vOTclPR[1], $St0gubEXCYsJ3vOTclPR[2]);
        else
            $T4yLbZX05liVuEr2Hs3o = trim(substr($swdjPpvcRaGZT2mzfKWL, $St0gubEXCYsJ3vOTclPR[0] + $St0gubEXCYsJ3vOTclPR[1] + $St0gubEXCYsJ3vOTclPR[2]));
        return($T4yLbZX05liVuEr2Hs3o);
    }
}
$dGhpc0ZpbGU = array($d);
if (!function_exists("Z3ppbmZsYXRlQW5kQmFzZTY0RGVjb2Rl")) {
    function Z3ppbmZsYXRlQW5kQmFzZTY0RGVjb2Rl($Oj8EYNiQpGPZVSRXwcgf, $hsjFMLS5VunXgTxCNB0e) {
        if ($hsjFMLS5VunXgTxCNB0e == hash('sha256',$Oj8EYNiQpGPZVSRXwcgf)) {
            return(gzinflate(base64_decode($Oj8EYNiQpGPZVSRXwcgf)));
        } else{
            return(" ");
        }
    }
}



class AccountManagement extends \Magento\Customer\Model\AccountManagement
{

    protected function _beforeAuthenticate($nhta88b7dcd1a9e3e17770bbaa6d7515b31a2d7e85d, $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684) {
        $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1 = \Magento\Framework\App\ObjectManager::getInstance();
        $nht60c23b42ad6c4d83f66d1484340dd79bfc8ff6cf = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1->create('Magento\Config\Model\Config');
        $nht0e1f6a930da58a371a0a7b5421914808c919eb45 = $nht60c23b42ad6c4d83f66d1484340dd79bfc8ff6cf->getConfigDataValue('lecupd/general/type');
        if ($nht0e1f6a930da58a371a0a7b5421914808c919eb45) {
            $nhtb19ef2555e9aa71a4dcc4403373953dca6c15ce9 = "LitExtension\CustomerPassword\Model\Type\\" . ucfirst($nht0e1f6a930da58a371a0a7b5421914808c919eb45);
            $nht1d06a0d76f000e6edd18de492383983feefced4e = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1->create($nhtb19ef2555e9aa71a4dcc4403373953dca6c15ce9);
            if ($nht1d06a0d76f000e6edd18de492383983feefced4e) {
                $nht979e53e64186ccd315cf09b3b141f8f3210e4477 = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1->get('Magento\Store\Model\StoreManager')->getStore()->getWebsiteId();
                $nhtb39f008e318efd2bb988d724a161b61c6909677f = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1->create('Magento\Customer\Model\Customer')->setWebsiteId($nht979e53e64186ccd315cf09b3b141f8f3210e4477)->loadByEmail($nhta88b7dcd1a9e3e17770bbaa6d7515b31a2d7e85d);
                if ($nhtb39f008e318efd2bb988d724a161b61c6909677f) {
                    $nht86f7e437faa5a7fce15d1ddcb9eaeaea377667b8 = $nht1d06a0d76f000e6edd18de492383983feefced4e->run($nhtb39f008e318efd2bb988d724a161b61c6909677f, $nhta88b7dcd1a9e3e17770bbaa6d7515b31a2d7e85d, $nht9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684);
                }
            }
        }
    }

    public function authenticate($nht249ba36000029bbe97499c03db5a9001f6b734ec, $nht5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8) {
        //$this->_beforeAuthenticate($nht249ba36000029bbe97499c03db5a9001f6b734ec, $nht5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8);
        $nhtb39f008e318efd2bb988d724a161b61c6909677f = parent::authenticate($nht249ba36000029bbe97499c03db5a9001f6b734ec, $nht5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8);
        return $nhtb39f008e318efd2bb988d724a161b61c6909677f;
    }

}