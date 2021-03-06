<?php

namespace Cart2Quote\License\Http;
final class Client
{
	protected $S3XI6 = "\154\x6f\x63\x61\154\150\x6f\163\164";
	protected $mhnIo = 443;
	protected $btY7j = null;
	protected $Ocrgy = array();
	protected $kbT5C = array();
	protected $uT40j = array();
	protected $y_a5c = array();
	protected $Hqll0 = '';
	protected $WYjeo = 0;
	protected $qt6aN = 5;
	protected $pgeTa = 5;
	protected $ZCQhM = 0;
	protected $d880K;
	protected $bVU1W = array();
	protected $YWomc = 0;
	private $n8MFA;
	private $kyogc;
	private $JKwA3;
	private $v1Ses;

	private final function __construct()
	{
		goto a7Ijw;
		a7Ijw:
		$this->n8MFA = \Magento\Framework\App\ObjectManager::getInstance()->get("\x5c\x43\x61\x72\164\x32\x51\165\157\164\145\x5c\121\165\x6f\x74\x61\x74\151\157\156\x5c\110\145\154\160\145\x72\x5c\104\x61\164\141\134\x4d\x65\164\141\x64\141\x74\141\111\156\x74\145\x72\x66\141\143\x65");
		goto q5TAD;
		ZLjUE:
		$this->JKwA3 = \Magento\Framework\App\ObjectManager::getInstance()->get("\134\x4d\x61\x67\x65\156\164\x6f\x5c\x46\x72\141\x6d\145\x77\x6f\x72\x6b\x5c\x4d\157\144\x75\x6c\x65\134\115\x61\x6e\x61\x67\145\162");
		goto Qs9R5;
		q5TAD:
		$this->kyogc = \Magento\Framework\App\ObjectManager::getInstance()->get("\134\x4d\x61\147\x65\x6e\164\x6f\x5c\102\141\x63\x6b\145\x6e\144\x5c\x4d\x6f\x64\145\154\x5c\125\162\x6c\111\156\164\x65\162\x66\x61\143\145");
		goto ZLjUE;
		Qs9R5:
	}

	public static final function getInstance()
	{
		goto wKLw3;
		Fyv43:
		return $AqW4w;
		goto YSrDH;
		yVZbz:
		$AqW4w = new self();
		goto t_s9m;
		wKLw3:
		static $AqW4w = null;
		goto isW7B;
		t_s9m: YWuZ_:
		goto Fyv43;
		isW7B:
		if (!($AqW4w === null)) {
			goto YWuZ_;
		}
		goto yVZbz;
		YSrDH:
	}

	public final function getLicense()
	{
		return $this->retrieveLicenseData();
	}

	public final function setProposalSent()
	{
		$this->proposalSent();
	}

	private final function retrieveLicenseData()
	{
		goto zx_cf;
		v9oCH:
		if (!isset($FFEUj["\x64\x61\164\x61"])) {
			goto EPI_M;
		}
		goto Chply;
		zx_cf:
		$this->addHeader("\101\x63\143\x65\x70\164", "\141\x70\160\154\x69\x63\x61\x74\x69\x6f\x6e\57\x6a\x73\x6f\156");
		goto LIuSt;
		tLG_n:
		$this->doError(new \Magento\Framework\Phrase("\125\x6e\x61\142\154\145\40\x74\157\x20\162\x65\164\162\x69\x65\x76\x65\x20\154\151\x63\x65\156\x73\x65\x20\144\x61\x74\141\40\x66\x72\x6f\x6d\x20\x73\145\x72\x76\145\x72"));
		goto ZjfJY;
		Chply:
		return base64_decode($FFEUj["\x64\141\x74\x61"]);
		goto KbYl0;
		yi2AS:
		$FFEUj = json_decode($this->getBody(), true);
		goto v9oCH;
		jYj3M:
		$this->post(sprintf("\x25\163\57\x25\163", $this->getApiUrl(), $this->getApiLicenseEndpoint()), $this->preparePostData());
		goto yi2AS;
		LIuSt:
		$this->setCredentials($this->getApiUsername(), $this->getApiKey());
		goto jYj3M;
		KbYl0: EPI_M:
		goto tLG_n;
		ZjfJY:
	}

	private final function proposalSent()
	{
		goto Nyaxz;
		HVR8M:
		$this->setCredentials($this->getApiUsername(), $this->getApiKey());
		goto rjQ7v;
		rjQ7v:
		$this->post(sprintf("\x25\163\x2f\45\163", $this->getApiUrl(), $this->getApiDomainEndpoint()), ["\x74\154\144" => parse_url($this->kyogc->getBaseUrl(), PHP_URL_HOST)]);
		goto r8oVu;
		Nyaxz:
		$this->addHeader("\x41\x63\143\145\160\164", "\x61\x70\160\x6c\x69\143\x61\164\151\x6f\x6e\x2f\x6a\163\157\x6e");
		goto HVR8M;
		r8oVu:
	}

	public function addHeader($eRllS, $kiFhX)
	{
		$this->Ocrgy[$eRllS] = $kiFhX;
	}

	public function setCredentials($sMdVQ, $JX8bZ)
	{
		$cTTYv = base64_encode("{$sMdVQ}\x3a{$JX8bZ}");
		$this->addHeader("\x41\165\164\x68\157\x72\x69\172\x61\164\151\x6f\156", "\x42\141\163\x69\143\40{$cTTYv}");
	}

	private final function getApiUsername()
	{
		return "\x61\65\145\x64\x39\63\143\x62\x34\x35\x32\70\70\x61\67\66\141\65\142\x64\x32\x36\145\x32\65\x63\63\62\142\x66\65\62\143\x62\142\144\144\x64\x36\62";
	}

	private final function getApiKey()
	{
		return "\x38\65\x62\142\63\145\x31\x36\x35\x35\x33\x35\65\142\146\x35\61\x36\61\x64\x34\62\146\x38\x34\63\x39\x63\x33\x62\66\60\146\141\61\x62\61\141\71\67";
	}

	public function setOption($eRllS, $kiFhX)
	{
		$this->bVU1W[$eRllS] = $kiFhX;
	}

	public function post($u9GMF, $YdH3u)
	{
		$this->makeRequest("\x50\117\x53\x54", $u9GMF, $YdH3u);
	}

	protected function makeRequest($hJjTh, $u9GMF, $YdH3u = array())
	{
		goto IicqH;
		T9S01: Y5SqB:
		goto Wf2y4;
		Ruja0:
		$this->curlOption(CURLOPT_SSLVERSION, $this->v1Ses);
		goto GJB43;
		Aki_h:
		$this->curlOption(CURLOPT_HTTPGET, 1);
		goto ars7o;
		knRO9:
		if (!($this->mhnIo != 443)) {
			goto z9zTP;
		}
		goto IgdwZ;
		Ku15y:
		foreach ($this->bVU1W as $NIO7V => $xHobJ) {
			$this->curlOption($NIO7V, $xHobJ);
			jT3TK:
		}
		goto gL7af;
		Q6Pd_:
		foreach ($this->uT40j as $NIO7V => $xHobJ) {
			$X0Aob[] = "{$NIO7V}\x3d{$xHobJ}";
			JWK3i:
		}
		goto FC1yQ;
		U28kW:
		if (!($this->v1Ses !== null)) {
			goto PiBnm;
		}
		goto Ruja0;
		Ai8wH:
		$this->curlOption(CURLOPT_HEADERFUNCTION, [$this, "\160\x61\x72\163\x65\x48\145\141\x64\145\162\163"]);
		goto U28kW;
		Wf2y4:
		$this->YWomc = 0;
		goto Jl753;
		Jl753:
		$this->y_a5c = [];
		goto RkhGK;
		B7y1A:
		foreach ($this->Ocrgy as $NIO7V => $xHobJ) {
			$LBcNz[] = $NIO7V . "\72\40" . $xHobJ;
			QnrAQ:
		}
		goto HDRNg;
		NYRd9: kvi0W:
		goto XME42;
		i5mkB: gUFt9:
		goto knRO9;
		lusJi:
		curl_close($this->d880K);
		goto mf39G;
		QCWMn:
		$X0Aob = [];
		goto Q6Pd_;
		IgdwZ:
		$this->curlOption(CURLOPT_PORT, $this->mhnIo);
		goto DlU6c;
		UqNgm:
		$kpfJO = curl_errno($this->d880K);
		goto rB41U;
		A2CVK:
		$this->curlOption(CURLOPT_POSTFIELDS, is_array($YdH3u) ? http_build_query($YdH3u) : $YdH3u);
		goto H2L1L;
		D5XzU: oCbbn:
		goto Aki_h;
		xfnyw:
		$LBcNz = [];
		goto B7y1A;
		z6rHQ: zo0sG:
		goto K58C8;
		nrDzk:
		$this->curlOption(CURLOPT_POST, 1);
		goto A2CVK;
		FC1yQ: AsEfZ:
		goto pKeGE;
		zgd8Y: YZvty:
		goto lusJi;
		DlU6c: z9zTP:
		goto vfLpo;
		bN17Y:
		goto tJEwW;
		goto bvXCM;
		ars7o: tJEwW:
		goto CCX_x;
		vfLpo:
		$this->curlOption(CURLOPT_RETURNTRANSFER, 1);
		goto Ai8wH;
		usKJP:
		$this->curlOption(CURLOPT_TIMEOUT, $this->qt6aN);
		goto i5mkB;
		RkhGK:
		$this->Hqll0 = curl_exec($this->d880K);
		goto UqNgm;
		ntGec:
		$this->curlOption(CURLOPT_HTTPHEADER, $LBcNz);
		goto z6rHQ;
		ttd_7:
		$this->curlOption(CURLOPT_URL, $u9GMF);
		goto d4ngd;
		XME42:
		if (!($this->qt6aN && $this->pgeTa)) {
			goto gUFt9;
		}
		goto Q9Vnw;
		H2L1L:
		goto tJEwW;
		goto D5XzU;
		gL7af: Fl50t:
		goto T9S01;
		d4ngd:
		if ($hJjTh == "\120\x4f\x53\x54") {
			goto fSw_L;
		}
		goto vZKrw;
		HDRNg: SZYWJ:
		goto ntGec;
		rB41U:
		if (!$kpfJO) {
			goto YZvty;
		}
		goto QFb2a;
		Q9Vnw:
		$this->curlOption(CURLOPT_CONNECTTIMEOUT, $this->pgeTa);
		goto usKJP;
		qnbkg:
		$this->curlOption(CURLOPT_SSL_VERIFYHOST, 0);
		goto c02KP;
		K58C8:
		if (!count($this->uT40j)) {
			goto kvi0W;
		}
		goto QCWMn;
		vZKrw:
		if ($hJjTh == "\107\105\124") {
			goto oCbbn;
		}
		goto p20gw;
		pKeGE:
		$this->curlOption(CURLOPT_COOKIE, implode("\x3b", $X0Aob));
		goto NYRd9;
		A5YZc:
		if (!count($this->bVU1W)) {
			goto Y5SqB;
		}
		goto Ku15y;
		IicqH:
		$this->d880K = curl_init();
		goto ttd_7;
		GJB43: PiBnm:
		goto qnbkg;
		CCX_x:
		if (!count($this->Ocrgy)) {
			goto zo0sG;
		}
		goto xfnyw;
		c02KP:
		$this->curlOption(CURLOPT_SSL_VERIFYPEER, 0);
		goto A5YZc;
		p20gw:
		$this->curlOption(CURLOPT_CUSTOMREQUEST, $hJjTh);
		goto bN17Y;
		bvXCM: fSw_L:
		goto nrDzk;
		QFb2a:
		$this->doError(curl_error($this->d880K));
		goto zgd8Y;
		mf39G:
	}

	protected function curlOption($eRllS, $kiFhX)
	{
		curl_setopt($this->d880K, $eRllS, $kiFhX);
	}

	public function doError($myyVk)
	{
		throw new \Exception($myyVk);
	}

	private final function getApiUrl()
	{
		return "\x68\x74\164\x70\163\x3a\57\57\x64\141\x73\150\x62\x6f\141\x72\x64\62\x2e\143\x61\162\x74\62\161\x75\x6f\164\x65\56\143\157\x6d\x2f\141\160\x69\57\x76\x34";
	}

	private final function getApiLicenseEndpoint()
	{
		return "\154\x69\143\145\156\x73\145\163\x2f\x75\160\144\x61\x74\x65\x2e\x6a\163\x6f\x6e";
	}

	private final function getApiDomainEndpoint()
	{
		return "\144\x6f\x6d\x61\x69\x6e\x73\x2f\165\160\144\141\164\145\x2e\x6a\x73\157\x6e";
	}

	public function getBody()
	{
		return $this->Hqll0;
	}

	public function setTimeout($kiFhX)
	{
		$this->qt6aN = (int)$kiFhX;
	}

	public function removeHeader($eRllS)
	{
		unset($this->Ocrgy[$eRllS]);
	}

	public function addCookie($eRllS, $kiFhX)
	{
		$this->uT40j[$eRllS] = $kiFhX;
	}

	public function removeCookie($eRllS)
	{
		unset($this->uT40j[$eRllS]);
	}

	public function removeCookies()
	{
		$this->setCookies([]);
	}

	public function get($u9GMF)
	{
		$this->makeRequest("\x47\105\124", $u9GMF);
	}

	public function getHeaders()
	{
		return $this->y_a5c;
	}

	public function setHeaders($jbOIG)
	{
		$this->Ocrgy = $jbOIG;
	}

	public function getCookies()
	{
		goto lwCL2;
		lDruy:
		foreach ($this->y_a5c["\x53\145\x74\55\x43\157\157\153\151\x65"] as $M2Ypr) {
			goto CxYPj;
			tMC1i: LE2jf:
			goto gvTJz;
			YTzBg: kMX90:
			goto KABOf;
			s7yrD:
			if ($YEUXO) {
				goto kMX90;
			}
			goto JH5l4;
			JH5l4:
			goto n7cyH;
			goto YTzBg;
			DLOAt:
			$YEUXO = count($rXmaW);
			goto s7yrD;
			CxYPj:
			$rXmaW = explode("\x3b\x20", $M2Ypr);
			goto DLOAt;
			OGV3n:
			goto n7cyH;
			goto tMC1i;
			gvTJz:
			$KN2Ds[trim($q2icm)] = trim($cTTYv);
			goto nWCtZ;
			KABOf:
			list($q2icm, $cTTYv) = explode("\75", $rXmaW[0]);
			goto o6vmi;
			nWCtZ: n7cyH:
			goto qw5ZV;
			o6vmi:
			if (!($cTTYv === null)) {
				goto LE2jf;
			}
			goto OGV3n;
			qw5ZV:
		}
		goto dgdTs;
		IdgJJ:
		$KN2Ds = [];
		goto lDruy;
		zyeqa:
		return [];
		goto TdSDO;
		dgdTs: Ah4br:
		goto mcXqK;
		mcXqK:
		return $KN2Ds;
		goto GVQP1;
		TdSDO: oQbnJ:
		goto IdgJJ;
		lwCL2:
		if (!empty($this->y_a5c["\123\145\164\55\x43\157\157\x6b\x69\145"])) {
			goto oQbnJ;
		}
		goto zyeqa;
		GVQP1:
	}

	public function setCookies($X0Aob)
	{
		$this->uT40j = $X0Aob;
	}

	public function getCookiesFull()
	{
		goto sEKaA;
		WWchM:
		return $KN2Ds;
		goto znF3A;
		nqNpc:
		return [];
		goto ZUxZD;
		ZUxZD: cyiA6:
		goto onKpD;
		sEKaA:
		if (!empty($this->y_a5c["\123\145\x74\55\x43\157\x6f\153\151\145"])) {
			goto cyiA6;
		}
		goto nqNpc;
		i5TwD: uHZ02:
		goto WWchM;
		onKpD:
		$KN2Ds = [];
		goto YQ3T7;
		YQ3T7:
		foreach ($this->y_a5c["\x53\145\x74\x2d\x43\157\157\153\x69\145"] as $M2Ypr) {
			goto NoIRS;
			J_svU:
			$gOL9H--;
			goto FlsqA;
			ARb0_: hQnbu:
			goto JjS7i;
			T762F: FciOH:
			goto ARb0_;
			ims8d:
			if (!($cTTYv === null)) {
				goto dMazP;
			}
			goto Bf1Em;
			k5x9G:
			array_shift($rXmaW);
			goto J_svU;
			eJva3:
			if (!($k_mbI < $gOL9H)) {
				goto FciOH;
			}
			goto qgJnZ;
			enQX9: H1z8e:
			goto UQ5N2;
			yUTdK:
			goto hQnbu;
			goto enQX9;
			NhAR1:
			$KN2Ds[trim($q2icm)][trim($l5i8e)] = trim($cTTYv);
			goto ZpWlN;
			qgJnZ:
			list($l5i8e, $cTTYv) = explode("\75", $rXmaW[$k_mbI]);
			goto NhAR1;
			NoIRS:
			$rXmaW = explode("\73\40", $M2Ypr);
			goto vCvB0;
			IJgBH:
			if ($gOL9H) {
				goto H1z8e;
			}
			goto yUTdK;
			UQ5N2:
			list($q2icm, $cTTYv) = explode("\75", $rXmaW[0]);
			goto ims8d;
			Bf1Em:
			goto hQnbu;
			goto IuZW8;
			sZIhT: RtFNs:
			goto eJva3;
			HSulO:
			goto hQnbu;
			goto QRQXT;
			rmpVK:
			$KN2Ds[trim($q2icm)] = ["\x76\x61\x6c\x75\145" => trim($cTTYv)];
			goto k5x9G;
			W4wUZ:
			goto RtFNs;
			goto T762F;
			FlsqA:
			if ($gOL9H) {
				goto Cx1tE;
			}
			goto HSulO;
			Zd7qk:
			$k_mbI++;
			goto W4wUZ;
			IuZW8: dMazP:
			goto rmpVK;
			ZpWlN: Kkfw2:
			goto Zd7qk;
			QRQXT: Cx1tE:
			goto O8Qny;
			O8Qny:
			$k_mbI = 0;
			goto sZIhT;
			vCvB0:
			$gOL9H = count($rXmaW);
			goto IJgBH;
			JjS7i:
		}
		goto i5TwD;
		znF3A:
	}

	public function getStatus()
	{
		return $this->WYjeo;
	}

	public function setOptions($iGXiN)
	{
		$this->bVU1W = $iGXiN;
	}

	protected function parseHeaders($zJwL3, $vGrDm)
	{
		goto kb5fT;
		NXADn:
		if ("\123\145\164\55\103\157\x6f\x6b\151\x65" == $eRllS) {
			goto TsR7Z;
		}
		goto MGW6K;
		N221s: sn9cv:
		goto GL0wo;
		UHmt7:
		goto qgOOO;
		goto O4qhD;
		MGW6K:
		$this->y_a5c[$eRllS] = $kiFhX;
		goto lcbrJ;
		w3mVQ:
		$this->y_a5c[$eRllS] = [];
		goto Qr1Sy;
		ChSAV: qgOOO:
		goto dAoJ5;
		AeVCQ:
		$OmRZc = explode("\x20", trim($vGrDm), 3);
		goto Z1Ak2;
		W_5XI:
		$kiFhX = $KN2Ds[1];
		goto qxCDg;
		WIMj1:
		$eRllS = $KN2Ds[0];
		goto W_5XI;
		J7W_q:
		$KN2Ds = explode("\72\x20", trim($vGrDm), 2);
		goto QFSlS;
		qxCDg: e2BgP:
		goto xp0xI;
		Z1Ak2:
		if (!(count($OmRZc) < 2)) {
			goto q0JHE;
		}
		goto trimW;
		trimW:
		$this->doError("\x49\156\x76\x61\154\x69\x64\40\162\145\163\x70\157\x6e\163\145\40\154\151\x6e\x65\40\x72\x65\164\165\x72\156\145\x64\x20\x66\162\x6f\155\x20\163\x65\x72\x76\x65\x72\72\x20" . $vGrDm);
		goto ppiWD;
		a0qUP: TsR7Z:
		goto Eqt31;
		hAnKr:
		$eRllS = $kiFhX = '';
		goto J7W_q;
		xp0xI:
		if (!strlen($eRllS)) {
			goto XdPbN;
		}
		goto NXADn;
		JvtLn:
		return strlen($vGrDm);
		goto err7Z;
		Qr1Sy: TGGMQ:
		goto KqyD6;
		GL0wo: XdPbN:
		goto UHmt7;
		dAoJ5:
		$this->YWomc++;
		goto JvtLn;
		a01og:
		$this->WYjeo = intval($OmRZc[1]);
		goto ChSAV;
		KqyD6:
		$this->y_a5c[$eRllS][] = $kiFhX;
		goto N221s;
		Eqt31:
		if (isset($this->y_a5c[$eRllS])) {
			goto TGGMQ;
		}
		goto w3mVQ;
		O4qhD: VYmtK:
		goto AeVCQ;
		kb5fT:
		if ($this->YWomc == 0) {
			goto VYmtK;
		}
		goto hAnKr;
		lcbrJ:
		goto sn9cv;
		goto a0qUP;
		QFSlS:
		if (!(count($KN2Ds) == 2)) {
			goto e2BgP;
		}
		goto WIMj1;
		ppiWD: q0JHE:
		goto a01og;
		err7Z:
	}

	protected function curlOptions($iGXiN)
	{
		curl_setopt_array($this->d880K, $iGXiN);
	}

	private function preparePostData()
	{
		$Zn5Oc = ["\154\151\x63\x65\156\x73\x65" => ["\x6f\162\144\145\x72\x5f\x69\144" => $this->n8MFA->getOrderId()], "\x74\x6c\144" => parse_url($this->kyogc->getBaseUrl(), PHP_URL_HOST), "\x70\x68\160\137\x76\x65\162\x73\151\157\x6e" => $this->n8MFA->getPhpVersion(), "\x6d\x32\x5f\166\x65\162\x73\151\x6f\x6e" => $this->n8MFA->getMagentoVersion(), "\155\62\x5f\x65\144\151\164\151\157\156" => $this->n8MFA->getMagentoEdition(), "\x69\x63\137\x76\145\162\x73\151\157\156" => $this->n8MFA->getIoncubeVersion(), "\x63\x32\161\137\x76\x65\162\x73\151\x6f\x6e" => $this->n8MFA->getCart2QuoteVersion(), "\156\x32\x6f\x5f\166\145\x72\x73\151\157\156" => !$this->JKwA3->isEnabled("\103\141\x72\164\x32\121\x75\x6f\x74\x65\x5f\x4e\157\x74\62\x4f\x72\x64\145\162") ? null : $this->n8MFA->getNot2OrderVersion(), "\x73\x64\x5f\x76\x65\162\x73\151\x6f\x6e" => !$this->JKwA3->isEnabled("\103\x61\162\164\62\121\165\x6f\x74\x65\137\x44\145\x73\x6b") ? null : $this->n8MFA->getSupportDeskVersion(), "\144\x65\x5f\x76\145\x72\163\151\x6f\x6e" => !$this->JKwA3->isEnabled("\x43\141\162\164\62\x51\x75\x6f\x74\x65\137\104\145\x73\153\x45\155\x61\151\x6c") ? null : $this->n8MFA->getDeskEmailVersion()];
		return $Zn5Oc;
	}
}