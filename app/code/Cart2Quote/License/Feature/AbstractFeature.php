<?php
 namespace Cart2Quote\License\Feature; abstract class AbstractFeature { private static $allowedInstanceRequesters = array(\Cart2Quote\Features\Feature\FeatureList::class, \Cart2Quote\License\Model\License::class); protected $license; protected $lite; protected $starter; protected $business; protected $enterprise; protected $trial; protected $unreachable; protected $opensource; protected $corporate; protected $configs = array(); protected $plugins = array(); protected $defaultAllowedStates = array(\Cart2Quote\License\Model\License::ACTIVE_STATE, \Cart2Quote\License\Model\License::PENDING_STATE, \Cart2Quote\License\Model\License::UNREACHABLE); private function __construct() { $this->init(); } public static final function getInstance($Co7Pn) { goto IOvGL; tPXPj: $a_cRG[$TwWSG] = new $TwWSG(); goto zKgdH; x3O64: return $a_cRG[$TwWSG]; goto ZEjbO; IOvGL: static $a_cRG = array(); goto qf7wk; neUNX: goto JZr6q; goto m2cym; bODXC: if (in_array(get_class($Co7Pn), self::$allowedInstanceRequesters)) { goto HjJUw; } goto TsCjR; GCVHC: if (isset($a_cRG[$TwWSG])) { goto Xy5zn; } goto bODXC; Q267y: Xy5zn: goto x3O64; m2cym: HjJUw: goto tPXPj; qf7wk: $TwWSG = get_called_class(); goto GCVHC; zKgdH: JZr6q: goto Q267y; TsCjR: return null; goto neUNX; ZEjbO: } public final function isAllowed() { return $this->isAllowedForState() && $this->isAllowedForType(); } private final function isAllowedForState() { return in_array(\Cart2Quote\License\Model\License::getInstance()->getLicenseState(), $this->allowedStates()); } protected abstract function allowedStates(); private final function isAllowedForType() { $V5OIz = \Cart2Quote\License\Model\License::getInstance()->getEdition(); return $this->getEditionsLevel($V5OIz) >= $this->allowedEdition(); } public final function isAllowedForEdition($V5OIz = "\154\x69\164\x65") { $XJWKD = \Cart2Quote\License\Model\License::getInstance()->getEdition(); return $this->getEditionsLevel($XJWKD) >= $this->getEditionsLevel($V5OIz); } private final function getEditionsLevel($V5OIz) { goto FVRzy; SgHY_: cQvqm: goto K0ISh; FVRzy: if (!isset($this->{$V5OIz})) { goto cQvqm; } goto s12SH; s12SH: return $this->{$V5OIz}; goto SgHY_; K0ISh: return 0; goto bR4H8; bR4H8: } protected abstract function allowedEdition(); public final function getPlugins() { goto fVY7g; DMyw2: return $this->plugins; goto EfuYk; TtlZ_: XK46I: goto DMyw2; fVY7g: foreach (array_keys($this->plugins) as $Dncnk) { goto O3wgt; O3wgt: foreach ($this->plugins[$Dncnk]["\x70\x6c\x75\x67\151\x6e\163"] as $UyZTs => $a414z) { goto zaqm3; PwnvP: $this->plugins[$Dncnk]["\x70\x6c\165\x67\151\156\163"][$OUFxf->getRandomString(12)] = $a414z; goto x2rvG; zaqm3: $OUFxf = new \Magento\Framework\Math\Random(); goto PwnvP; gsZmf: vixZN: goto APwcE; x2rvG: unset($this->plugins[$Dncnk]["\x70\x6c\165\x67\151\156\x73"][$UyZTs]); goto gsZmf; APwcE: } goto ANeMm; ANeMm: KSLQP: goto fibno; fibno: waK1m: goto hwdW8; hwdW8: } goto TtlZ_; EfuYk: } public final function getConfigs() { return $this->configs; } private final function init() { goto vlt0s; yXbS0: tx5UF: goto B6Y1i; vlt0s: $NJFAU = ["\154\x69\164\x65" => 0, "\163\x74\141\x72\164\145\162" => 5, "\142\x75\x73\x69\x6e\x65\x73\163" => 10, "\x65\156\164\x65\162\160\162\x69\163\x65" => 20, "\x74\162\x69\141\x6c" => 20, "\x75\156\162\x65\141\143\x68\x61\x62\154\x65" => 20, "\x6f\x70\x65\x6e\163\x6f\165\162\143\x65" => 40, "\143\157\162\160\157\x72\x61\164\145" => 50]; goto ydxlS; ydxlS: foreach ($NJFAU as $V5OIz => $ZU5Y3) { $this->{$V5OIz} = $ZU5Y3; kSzV7: } goto yXbS0; B6Y1i: } }