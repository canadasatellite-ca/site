<?php

namespace Cart2Quote\License\Security;

use Cart2Quote\License\Security\Crypto\Mcrypt;
use Cart2Quote\License\Security\Crypto\OpenSsl;
use InvalidArgumentException;

final class Security
{
	public static $uIkrk = "\x73\150\141\61";
	protected static $CsD5d;
	protected static $XDi24;

	public static function setHash($vJy8C)
	{
		static::$uIkrk = $vJy8C;
	}

	public static function randomBytes($tdMrL)
	{
		goto EN230;
		g6M0E:
		trigger_error("\x6f\160\145\x6e\x73\x73\154\40\x77\x61\x73\40\x75\156\x61\x62\154\x65\x20\164\157\40\165\x73\145\x20\141\x20\x73\x74\x72\x6f\156\147\40\x73\x6f\x75\162\x63\145\x20\157\x66\x20\145\x6e\164\162\157\x70\x79\56\x20" . "\103\157\156\x73\151\144\x65\162\x20\165\160\x64\141\x74\x69\156\x67\40\x79\x6f\x75\162\x20\163\171\163\164\x65\155\40\x6c\x69\x62\x72\x61\162\151\145\x73\x2c\x20\157\162\x20\x65\x6e\163\165\x72\151\156\x67\40" . "\171\x6f\x75\40\x68\141\x76\145\40\155\157\162\145\40\x61\x76\x61\151\154\x61\142\x6c\145\40\145\156\x74\162\x6f\x70\x79\56", E_USER_WARNING);
		goto o4V_3;
		RUaVw:
		trigger_error("\x59\157\x75\40\144\x6f\x20\x6e\157\164\x20\150\x61\166\x65\x20\x61\40\x73\x61\x66\145\x20\x73\x6f\x75\162\143\x65\40\157\146\x20\x72\x61\x6e\144\x6f\x6d\40\144\x61\x74\141\40\141\166\141\151\154\x61\x62\154\145\56\x20" . "\111\156\x73\164\x61\154\154\40\x65\x69\164\x68\x65\162\40\164\150\145\40\x6f\160\145\x6e\x73\163\154\x20\x65\170\x74\x65\x6e\163\x69\157\156\54\x20\157\162\40\160\x61\162\x61\147\157\156\151\145\57\x72\x61\x6e\144\x6f\155\137\x63\157\x6d\x70\141\x74\56\40" . "\x46\x61\x6c\x6c\151\x6e\147\40\x62\x61\x63\153\40\164\157\x20\x61\x6e\40\151\x6e\x73\145\143\x75\x72\145\x20\x72\x61\x6e\144\157\155\40\163\157\165\x72\x63\145\x2e", E_USER_WARNING);
		goto xElQ4;
		o4V_3: CMKeW:
		goto SV7Mm;
		nU53a:
		$aLBO1 = openssl_random_pseudo_bytes($tdMrL, $mcz69);
		goto anEku;
		EN230:
		if (!function_exists("\162\141\156\144\x6f\155\137\x62\x79\164\145\163")) {
			goto T7gHt;
		}
		goto sWcfh;
		SV7Mm:
		return $aLBO1;
		goto I_H3M;
		sWcfh:
		return random_bytes($tdMrL);
		goto NXqYz;
		anEku:
		if ($mcz69) {
			goto CMKeW;
		}
		goto g6M0E;
		xElQ4:
		return static::insecureRandomBytes($tdMrL);
		goto PlEJF;
		XG4lQ:
		if (!function_exists("\x6f\160\x65\156\163\163\154\x5f\x72\x61\156\x64\x6f\155\x5f\x70\x73\x65\x75\x64\x6f\137\142\171\164\x65\163")) {
			goto SRbDl;
		}
		goto nU53a;
		I_H3M: SRbDl:
		goto RUaVw;
		NXqYz: T7gHt:
		goto XG4lQ;
		PlEJF:
	}

	public static function insecureRandomBytes($tdMrL)
	{
		goto gELIZ;
		VApWd:
		goto vZrMW;
		goto x134h;
		YNJ0l: vZrMW:
		goto ZNszP;
		ZNszP:
		if (!($lVdTD < $tdMrL)) {
			goto WqtlX;
		}
		goto NV8zW;
		GF9VH:
		$aLBO1 = substr($aLBO1, 0, $tdMrL);
		goto rYjXx;
		dvaKN:
		$lVdTD = strlen($aLBO1);
		goto VApWd;
		nwx5_:
		$aLBO1 = '';
		goto NpZXW;
		rYjXx:
		return pack("\x48\52", $aLBO1);
		goto hw6az;
		NpZXW:
		$lVdTD = 0;
		goto YNJ0l;
		x134h: WqtlX:
		goto GF9VH;
		gELIZ:
		$tdMrL *= 2;
		goto nwx5_;
		NV8zW:
		$aLBO1 .= static::hash(Text::uuid() . uniqid(mt_rand(), true), "\163\150\x61\65\61\x32", true);
		goto dvaKN;
		hw6az:
	}

	public static function hash($T7wGo, $GxlhI = null, $xLh8Q = false)
	{
		goto gASss;
		f9OZ6:
		if (!$xLh8Q) {
			goto Qy7Ig;
		}
		goto vO7TF;
		SEYVN: Qy7Ig:
		goto A2wV7;
		VEYDo:
		$xLh8Q = static::$CsD5d;
		goto l6a3_;
		A2wV7:
		return hash($GxlhI, $T7wGo);
		goto JMM9a;
		g7lrd:
		$GxlhI = strtolower($GxlhI);
		goto f9OZ6;
		vO7TF:
		if (is_string($xLh8Q)) {
			goto HVWPh;
		}
		goto VEYDo;
		l6a3_: HVWPh:
		goto N_do4;
		gASss:
		if (!empty($GxlhI)) {
			goto R7k7j;
		}
		goto j14aJ;
		TlG43: R7k7j:
		goto g7lrd;
		j14aJ:
		$GxlhI = static::$uIkrk;
		goto TlG43;
		N_do4:
		$T7wGo = $xLh8Q . $T7wGo;
		goto SEYVN;
		JMM9a:
	}

	public static function rijndael($XCKht, $BS5v9, $Fv6sF)
	{
		goto ws0hG;
		Oj97J:
		return $LthyU->rijndael($XCKht, $BS5v9, $Fv6sF);
		goto E4N5D;
		VnLwY:
		$LthyU = static::engine();
		goto Oj97J;
		S2ck3: N0jiF:
		goto OAlct;
		eDwJK: t1GQr:
		goto VwHxd;
		wfz_T:
		throw new InvalidArgumentException("\131\157\165\40\155\165\x73\x74\40\x73\160\x65\x63\x69\146\171\x20\x74\150\145\40\x6f\160\x65\162\x61\x74\x69\157\156\x20\146\x6f\x72\40\x53\x65\143\x75\x72\151\164\x79\72\72\162\x69\152\156\144\x61\145\x6c\50\x29\x2c\40\145\x69\x74\x68\145\x72\40\145\x6e\143\x72\x79\x70\164\x20\157\x72\40\x64\x65\x63\x72\171\160\x74");
		goto eDwJK;
		OAlct:
		if (!(empty($Fv6sF) || !in_array($Fv6sF, ["\145\156\143\x72\171\160\164", "\144\145\x63\162\171\x70\164"]))) {
			goto t1GQr;
		}
		goto wfz_T;
		f6T01:
		throw new InvalidArgumentException("\x59\157\x75\x20\x6d\165\163\x74\40\165\163\145\40\141\x20\x6b\x65\x79\x20\154\x61\162\147\145\x72\x20\x74\150\x61\x6e\40\63\x32\x20\x62\x79\x74\145\163\x20\x66\157\x72\40\x53\x65\x63\x75\x72\x69\164\x79\x3a\x3a\x72\x69\152\x6e\x64\141\145\154\x28\51");
		goto gFaZy;
		VwHxd:
		if (!(mb_strlen($BS5v9, "\x38\142\x69\164") < 32)) {
			goto OCvr8;
		}
		goto f6T01;
		ws0hG:
		if (!empty($BS5v9)) {
			goto N0jiF;
		}
		goto YXPwH;
		gFaZy: OCvr8:
		goto VnLwY;
		YXPwH:
		throw new InvalidArgumentException("\x59\x6f\165\40\143\x61\x6e\x6e\157\164\x20\165\x73\145\40\141\156\x20\x65\x6d\160\x74\171\x20\x6b\145\171\40\146\x6f\x72\40\123\145\143\165\x72\151\164\x79\x3a\72\162\x69\152\156\144\141\145\154\50\51");
		goto S2ck3;
		E4N5D:
	}

	public static function engine($HYTF9 = null)
	{
		goto h3Pfz;
		ly5TM: Uf_xq:
		goto BTqHS;
		hdzW2:
		if (!$HYTF9) {
			goto QKesP;
		}
		goto LfU3o;
		cPov0:
		goto mlqI2;
		goto UUrFy;
		p1kQT:
		$HYTF9 = new OpenSsl();
		goto reBid;
		K7ukn:
		if (!isset(static::$XDi24)) {
			goto Uf_xq;
		}
		goto IgyFL;
		GCTWb: weCu4:
		goto ii2AK;
		lJ8MD: ySVnC:
		goto hdzW2;
		h3Pfz:
		if (!($HYTF9 === null && static::$XDi24 === null)) {
			goto ySVnC;
		}
		goto bVpPW;
		LfU3o:
		static::$XDi24 = $HYTF9;
		goto Vnsta;
		sK_UW: mlqI2:
		goto lJ8MD;
		Vnsta: QKesP:
		goto K7ukn;
		E41Yx:
		if (extension_loaded("\155\x63\162\x79\x70\164")) {
			goto weCu4;
		}
		goto cPov0;
		ii2AK:
		$HYTF9 = new Mcrypt();
		goto sK_UW;
		IgyFL:
		return static::$XDi24;
		goto ly5TM;
		UUrFy: ZzHAL:
		goto p1kQT;
		BTqHS:
		throw new InvalidArgumentException("\116\x6f\40\143\157\x6d\x70\x61\164\151\x62\154\145\x20\x63\162\171\160\164\157\x20\x65\x6e\x67\151\156\x65\x20\x61\166\x61\x69\154\x61\x62\x6c\145\x2e\40" . "\114\157\141\x64\40\x65\151\x74\150\x65\x72\x20\x74\150\145\40\157\x70\145\156\163\x73\x6c\x20\157\162\x20\155\x63\x72\x79\x70\164\x20\145\x78\x74\145\156\163\151\157\156\x73");
		goto o4NBT;
		reBid:
		goto mlqI2;
		goto GCTWb;
		bVpPW:
		if (extension_loaded("\157\x70\x65\156\163\163\154")) {
			goto ZzHAL;
		}
		goto E41Yx;
		o4NBT:
	}

	public static function encrypt($U0gW5, $BS5v9, $pgWa1 = null)
	{
		goto i_PqC;
		kAiP3:
		$pgWa1 = static::$CsD5d;
		goto q10o0;
		sWQJU:
		$YY1Nd = hash_hmac("\x73\x68\x61\62\65\x36", $ADIGD, $BS5v9);
		goto ytclV;
		hxE4l:
		$LthyU = static::engine();
		goto qFxFp;
		qFxFp:
		$ADIGD = $LthyU->encrypt($U0gW5, $BS5v9);
		goto sWQJU;
		q10o0: MT8zd:
		goto Hrdrk;
		ytclV:
		return $YY1Nd . $ADIGD;
		goto n1Mvm;
		Hrdrk:
		$BS5v9 = mb_substr(hash("\163\x68\x61\x32\x35\66", $BS5v9 . $pgWa1), 0, 32, "\70\142\x69\x74");
		goto hxE4l;
		i_PqC:
		self::_checkKey($BS5v9, "\145\156\x63\x72\171\160\x74\50\51");
		goto bHjWC;
		bHjWC:
		if (!($pgWa1 === null)) {
			goto MT8zd;
		}
		goto kAiP3;
		n1Mvm:
	}

	protected static function _checkKey($BS5v9, $q_lAD)
	{
		goto CtPCT;
		CtPCT:
		if (!(mb_strlen($BS5v9, "\70\x62\x69\164") < 32)) {
			goto LxcYS;
		}
		goto UhoFp;
		UhoFp:
		throw new InvalidArgumentException(sprintf("\x49\156\x76\x61\x6c\151\x64\x20\x6b\145\171\40\x66\x6f\x72\40\x25\x73\54\40\153\145\x79\x20\x6d\165\x73\164\x20\x62\x65\40\141\164\x20\x6c\x65\141\x73\x74\x20\x32\65\66\x20\x62\151\x74\163\x20\x28\63\x32\x20\142\171\164\x65\163\x29\x20\x6c\x6f\156\147\x2e", $q_lAD));
		goto vYcIJ;
		vYcIJ: LxcYS:
		goto fTiv8;
		fTiv8:
	}

	public static function decrypt($CNtpO, $BS5v9, $pgWa1 = null)
	{
		goto pDkLG;
		UzZFx:
		$CNtpO = mb_substr($CNtpO, $B426O, null, "\70\x62\151\x74");
		goto Gy7K_;
		cfRLZ:
		$YY1Nd = mb_substr($CNtpO, 0, $B426O, "\70\x62\151\x74");
		goto UzZFx;
		ZPfGW:
		if (!empty($CNtpO)) {
			goto MRKPD;
		}
		goto p4NQz;
		AMXMU: nIQ3E:
		goto kUsXs;
		PG63f:
		if (!($pgWa1 === null)) {
			goto H7vpa;
		}
		goto SjTgm;
		w07ui:
		$B426O = 64;
		goto cfRLZ;
		p4NQz:
		throw new InvalidArgumentException("\124\x68\145\40\144\141\164\141\x20\x74\157\40\x64\x65\143\x72\171\x70\x74\40\x63\x61\156\156\157\x74\x20\142\x65\40\145\155\x70\164\x79\56");
		goto Asyn7;
		LbEWD:
		return $LthyU->decrypt($CNtpO, $BS5v9);
		goto wRqi6;
		kUsXs:
		$LthyU = static::engine();
		goto LbEWD;
		SjTgm:
		$pgWa1 = static::$CsD5d;
		goto DBfir;
		Gy7K_:
		$tX2a1 = hash_hmac("\163\x68\x61\x32\65\66", $CNtpO, $BS5v9);
		goto JcmNv;
		HwM1N:
		$BS5v9 = mb_substr(hash("\x73\x68\141\x32\x35\66", $BS5v9 . $pgWa1), 0, 32, "\x38\142\x69\x74");
		goto w07ui;
		pDkLG:
		self::_checkKey($BS5v9, "\x64\145\143\162\x79\x70\164\x28\51");
		goto ZPfGW;
		JcmNv:
		if (static::_constantEquals($YY1Nd, $tX2a1)) {
			goto nIQ3E;
		}
		goto ePhm5;
		DBfir: H7vpa:
		goto HwM1N;
		ePhm5:
		return false;
		goto AMXMU;
		Asyn7: MRKPD:
		goto PG63f;
		wRqi6:
	}

	protected static function _constantEquals($YY1Nd, $Pjyev)
	{
		goto dUPQ5;
		ziMnC:
		if (!($rcbb2 !== $oXecl)) {
			goto qp9bQ;
		}
		goto ec3_I;
		mNNIl:
		$Kr4kg = 0;
		goto KqStN;
		eSM09:
		$rcbb2 = mb_strlen($YY1Nd, "\70\142\151\164");
		goto OCL6x;
		OBtRH: qp9bQ:
		goto mNNIl;
		FWFQm:
		$L0PxP++;
		goto q31pQ;
		dUPQ5:
		if (!function_exists("\x68\141\163\x68\x5f\145\x71\x75\x61\154\x73")) {
			goto biFzz;
		}
		goto KMQSy;
		KqStN:
		$L0PxP = 0;
		goto sVv4e;
		swZPK:
		$Kr4kg |= ord($YY1Nd[$L0PxP]) ^ ord($Pjyev[$L0PxP]);
		goto VDdnf;
		sVv4e: D3_LJ:
		goto A0bL1;
		ec3_I:
		return false;
		goto OBtRH;
		FK3Xr: biFzz:
		goto eSM09;
		A0bL1:
		if (!($L0PxP < $rcbb2)) {
			goto IUHkU;
		}
		goto swZPK;
		KMQSy:
		return hash_equals($YY1Nd, $Pjyev);
		goto FK3Xr;
		VDdnf: zB6x3:
		goto FWFQm;
		OCL6x:
		$oXecl = mb_strlen($Pjyev, "\70\x62\151\x74");
		goto ziMnC;
		q31pQ:
		goto D3_LJ;
		goto j7dlX;
		St5bJ:
		return $Kr4kg === 0;
		goto AQqJ3;
		j7dlX: IUHkU:
		goto St5bJ;
		AQqJ3:
	}

	public static function getSalt()
	{
		return static::$CsD5d;
	}

	public static function setSalt($xLh8Q)
	{
		static::$CsD5d = (string)$xLh8Q;
	}

	public static function salt($xLh8Q = null)
	{
		goto cuBCS;
		ktVJ1:
		return static::$CsD5d;
		goto ryejg;
		cuBCS:
		if (!($xLh8Q === null)) {
			goto vTBMZ;
		}
		goto ktVJ1;
		ryejg: vTBMZ:
		goto LNuAo;
		LNuAo:
		return static::$CsD5d = (string)$xLh8Q;
		goto P41Q0;
		P41Q0:
	}
}