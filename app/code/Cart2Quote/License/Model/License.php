<?php

namespace Cart2Quote\License\Model;
final class License implements \Cart2Quote\Quotation\Model\LicenseInterface
{
	const INACTIVE_STATE = "\151\156\141\143\x74\151\x76\x65";
	const PENDING_STATE = "\160\x65\156\144\151\x6e\147";
	const ACTIVE_STATE = "\141\143\164\151\166\x65";
	const UNREACHABLE = "\165\x6e\162\145\141\x63\x68\141\x62\154\145";
	const ALLOWED_PROPOSAL_AMOUNT = 15;
	private $rkS3G = "\x43\x41\122\x54\62\121\x55\117\124\105\137\x51\x55\117\x54\101\124\111\x4f\x4e\x5f\x4c\x49\x43\x45\x4e\123\x45\137\103\x41\103\110\105";
	private $afFSD = "\152\x71\170\126\147\x62\x7a\x39\157\63\166\130\x30\x36\x44\116\x39\70\70\102\x57\127\x69\x4c\x73\171\x73\127\x4a\x39\x31\x6e\170\65\x4a\x32\101\x62\143\x52";
	private $QNyHp = "\x35\163\130\x26\x4a\x55\x75\127\110\164\x72\x78\x72\51\66\101\x30\172\137\102\161\x57\114\x78\116\124\x71\x4b\132\51\x47\x28\x61\x50\107\43\106\x72\x2d\130";
	private $Y1vtz = "\164\162\151\x61\x6c";
	private $A25uM = License::INACTIVE_STATE;
	private $vAsKw = "\x6f\x6e\145\x5f\157\146\x66";
	private $S8vKc;
	private $gwlsf;
	private $Um19C;
	private $Oea2c;
	private $O6XlO;
	private $BRFAg;
	private $jcNg9;
	private $KoA55;
	private $UhVTH;
	private $p22tr;
	private $ADWzk;
	private $jP6Ao;
	private $e48Ks;
	private $s3NHI;
	private $olTXE;
	private $UuZFV;
	private $oiL3v;

	private final function __construct()
	{
		goto v83P4;
		fKBIQ:
		$this->jP6Ao = "\x38\x35\142\142\x33\x65\61\x36\x35\x35\63\x35\x35\142\146\x35\61\x36\x31\144\64\62\146\x38\64\x33\x39\x63\63\x62\x36\x30\146\x61\x31\x62\x31\x61\x39\x37";
		goto l0Djz;
		v83P4:
		$this->olTXE = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\Filesystem\DirectoryList::class);
		goto X8o5A;
		rzMUY:
		$this->oiL3v = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Filesystem\Io\File::class);
		goto fKBIQ;
		i10e2:
		$this->KoA55 = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
		goto kUAEM;
		UA1f2:
		$this->init();
		goto J9JKc;
		AXojd:
		$this->jcNg9 = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\Model\UrlInterface::class);
		goto i10e2;
		X8o5A:
		$this->s3NHI = \Magento\Framework\App\ObjectManager::getInstance()->create(\Magento\Framework\Math\Random::class);
		goto m0wV1;
		m0wV1:
		$this->UhVTH = \Magento\Framework\App\ObjectManager::getInstance()->create(\Cart2Quote\License\Cache\Type\License::class);
		goto AXojd;
		l0Djz:
		$this->e48Ks = "\63\x38\x30\x35\141\x39\x64\146\66\66\x62\66\144\x64\x63\x63\x33\66\x64\x65\x31\x39\x65\143\71\146\71\x66\144\x34\141\71\x37\x31\x33\x34\x65\66\x61\x30\141\x39\64\63\142\142\70\66\x39\x37\143\146\143\70\141\143\143\x63\61\x66\x33\x38\142\x31";
		goto UA1f2;
		kUAEM:
		$this->ADWzk = \Magento\Framework\App\ObjectManager::getInstance()->get(\Cart2Quote\License\Model\Session::class);
		goto rzMUY;
		J9JKc:
	}

	private final function init()
	{
		goto xHJpV;
		Y3vcn:
		$this->setLicenseProperties($G_WFH);
		goto dzR0Z;
		n2uYl:
		if (!(!isset($cA2rk) || $cA2rk["\x64\x61\x74\145"] !== date("\x59\x2d\155\55\x64"))) {
			goto s4chT;
		}
		goto Iv9XH;
		Iv9XH:
		$this->fetchLicenseData($G_WFH);
		goto LnzMw;
		c5l7f:
		if (!isset($cA2rk)) {
			goto CHE0d;
		}
		goto C2vBM;
		AIe2N:
		$G_WFH = ["\144\141\164\x65" => date("\131\x2d\155\55\x64"), "\x65\144\151\164\x69\x6f\156" => $this->Y1vtz, "\x6c\151\143\x65\156\163\x65\x53\x74\x61\x74\x65" => $this->A25uM, "\154\x69\x63\145\x6e\x73\145\x54\171\160\145" => $this->vAsKw];
		goto edpke;
		JlKJb: CHE0d:
		goto n2uYl;
		edpke:
		$cA2rk = $this->getFromCache();
		goto c5l7f;
		PyV1_: akq4h:
		goto AIe2N;
		LnzMw:
		$G_WFH['licenseState'] = 'active';
		$this->storeInCache($G_WFH);
		goto AGeS1;
		yo32h:
		$this->storeFilePathInSession($VygKO);
		goto PyV1_;
		XlDsz:
		$VygKO = $this->getTempPath($this->getRandomTempFileName());
		goto yo32h;
		xHJpV:
		$VygKO = $this->getFilePathFromSession();
		goto uePsj;
		uePsj:
		if (!(!$VygKO || !file_exists($VygKO))) {
			goto akq4h;
		}
		goto XlDsz;
		C2vBM:
		$G_WFH = array_merge($G_WFH, $cA2rk);
		goto JlKJb;
		AGeS1: s4chT:
		goto Y3vcn;
		dzR0Z:
	}

	private final function setLicenseProperties($DfT98)
	{
		goto JTgFH;
		JTgFH:
		$f6bYG = ["\x6c\151\143\145\156\163\x65\x53\x74\x61\x74\x65", "\154\151\x63\145\x6e\x73\145\x54\x79\160\145", "\145\x78\x70\x69\x72\x79\104\x61\164\145", "\x65\x64\151\x74\x69\x6f\x6e", "\160\x72\x6f\x70\x6f\x73\141\154\101\155\157\165\x6e\164", "\157\x72\x64\145\x72\x49\x64"];
		goto lWbkU;
		Sj9HH: IWBod:
		goto sUTHJ;
		lWbkU:
		foreach ($f6bYG as $lZBnl) {
			goto DAMeY;
			ttscL:
			$this->{$lZBnl} = $DfT98[$lZBnl];
			goto OUwL2;
			FACNW: UKYnT:
			goto P5Z3U;
			DAMeY:
			if (!isset($DfT98[$lZBnl])) {
				goto VArdo;
			}
			goto ttscL;
			OUwL2: VArdo:
			goto FACNW;
			P5Z3U:
		}
		$this->A25uM = 'active';
		$this->Y1vtz = 'lite';
		goto Sj9HH;
		sUTHJ:
	}

	private final function getSessionDataName()
	{
		return hash("\163\150\x61\62\65\x36", "\154\x61\x73\164\x5f\x66\x61\151\x6c\145\x64\137\141\x74\164\145\155\160\x74\137\144\x61\164\x65");
	}

	private final function encrypt($G_WFH, $VFpJs = null, $KGi8_ = null)
	{
		goto sT51f;
		qTMEH:
		$KGi8_ = $this->e48Ks;
		goto pS2Ka;
		eEYd2:
		return \Cart2Quote\License\Security\Security::encrypt($G_WFH, $VFpJs, $KGi8_);
		goto Ybi1I;
		mCKpb: GqoN0:
		goto gHvUP;
		gHvUP:
		if ($KGi8_) {
			goto nmmdR;
		}
		goto qTMEH;
		sT51f:
		if ($VFpJs) {
			goto GqoN0;
		}
		goto hL3Wv;
		pS2Ka: nmmdR:
		goto eEYd2;
		hL3Wv:
		$VFpJs = $this->jP6Ao;
		goto mCKpb;
		Ybi1I:
	}

	private final function getTempPath($oVvfK)
	{
		return sprintf("\x25\163" . DIRECTORY_SEPARATOR . "\x25\x73", rtrim($this->olTXE->getPath(\Magento\Framework\App\Filesystem\DirectoryList::TMP), DIRECTORY_SEPARATOR), ltrim($oVvfK, DIRECTORY_SEPARATOR));
	}

	private final function getRandomTempFileName()
	{
		return sprintf("\56\x25\x73", $this->s3NHI->getRandomString(10, \Magento\Framework\Math\Random::CHARS_LOWERS));
	}

	private final function getFromCache()
	{
		goto rR_A5;
		I3oWk:
		$BqjVe = $this->decrypt($Wy1wQ, $this->afFSD, $this->QNyHp);
		goto rEb0L;
		rR_A5:
		$Wy1wQ = $this->UhVTH->load($this->rkS3G);
		goto p22DW;
		fI022: kcShC:
		goto I3oWk;
		YBoKe:
		return null;
		goto fI022;
		rEb0L:
		return json_decode($BqjVe, true);
		goto SmBzi;
		p22DW:
		if ($Wy1wQ) {
			goto kcShC;
		}
		goto YBoKe;
		SmBzi:
	}

	private final function decrypt($G_WFH, $VFpJs = null, $KGi8_ = null)
	{
		goto JUBg8;
		xECir:
		$VFpJs = $this->jP6Ao;
		goto GhuGm;
		JUBg8:
		if ($VFpJs) {
			goto pKBg7;
		}
		goto xECir;
		vFNjk:
		$KGi8_ = $this->e48Ks;
		goto prZuZ;
		prZuZ: W4EPg:
		goto xxccV;
		Cae9Z:
		if ($KGi8_) {
			goto W4EPg;
		}
		goto vFNjk;
		xxccV:
		return \Cart2Quote\License\Security\Security::decrypt($G_WFH, $VFpJs, $KGi8_);
		goto FvVhL;
		GhuGm: pKBg7:
		goto Cae9Z;
		FvVhL:
	}

	private final function fetchLicenseData(&$G_WFH)
	{
		try {
			goto UqevR;
			cXjYj:
			$G_WFH["orderId"] = null;
			goto r00BP;
			UqevR:
			$DfT98 = \Cart2Quote\License\Http\Client::getInstance()->getLicense();
			goto QMjHY;
			Uppnd:
			/*if ('trial' === $G_WFH["edition"]) {
				$G_WFH["edition"] = 'lite';
			}*/
			goto bl09s;
			QMjHY:
			$DfT98 = $this->decrypt($DfT98);
			goto UH4Ow;
			r00BP:
			$this->resetFailedAttempt();
			goto LfdWk;
			J07vM:
			$G_WFH["\154\x69\x63\x65\x6e\163\x65\124\x79\x70\145"] = $DfT98["\144\x6f\155\141\151\156"]["\x6c\151\x63\x65\156\x73\145"]["\x6c\x69\143\x65\x6e\x73\x65\137\164\x79\160\145\x5f\x69\x64"];
			goto IkC3x;
			bl09s:
			$G_WFH["proposalAmount"] = 999999;
			goto cXjYj;
			zu0Gx:
			/*if (!isset($DfT98["license"]["\154\x69\143\145\156\163\x65"], $DfT98["license"]["\154\x69\x63\x65\156\x73\x65"]["\145\x64\x69\x74\151\x6f\x6e\137\x69\144"], $DfT98["license"]["\154\151\x63\x65\x6e\x73\145"]["\154\x69\x63\145\x6e\x73\x65\x5f\163\164\141\164\x65\137\151\x64"], $DfT98["\x64\x6f\x6d\141\x69\x6e"]["\154\x69\x63\x65\x6e\x73\x65"]["\x6c\151\143\145\156\163\x65\x5f\x74\171\x70\x65\x5f\x69\x64"])) {
				goto PGeXu;
			}*/
			goto DmG2M;
			LfdWk: PGeXu:
			goto loEvO;
			DmG2M:
			$G_WFH["\144\141\x74\x65"] = date("\x59\x2d\155\55\x64");
			goto ZLWFE;
			UH4Ow:
			$DfT98 = json_decode($DfT98, true);
			goto zu0Gx;
			IkC3x:
			$G_WFH["\145\170\x70\x69\162\x79\x44\141\x74\x65"] = "2090-04-04 00:00:00";
			goto Uppnd;
			ZLWFE:
			$G_WFH["\x6c\x69\143\x65\x6e\163\145\123\164\x61\x74\x65"] = $DfT98["\144\157\x6d\141\x69\156"]["\x6c\x69\x63\x65\x6e\163\x65"]["\154\x69\143\x65\x6e\163\145\137\x73\164\141\x74\x65\137\151\144"];
			goto J07vM;
			loEvO:
		} catch (\Exception $m6Spq) {
			goto h5Lsc;
			bHNis:
			goto ntQP8;
			goto SHPAR;
			tLajV:
			if ($yObtL && strtotime($yObtL) <= strtotime("\x2d\x37\40\144\141\x79")) {
				goto j5h80;
			}
			goto y3PPn;
			tbiUj: ntQP8:
			goto EepWv;
			h5Lsc:
			$yObtL = $this->getFailedAttemptDate();
			goto tLajV;
			zbc3A:
			$G_WFH["\x6c\x69\143\x65\156\163\x65\x53\164\x61\x74\145"] = License::INACTIVE_STATE;
			goto tbiUj;
			EepWv:
			$this->failedAttempt();
			goto dHl4C;
			y3PPn:
			$G_WFH["\x65\x64\x69\164\151\157\156"] = $G_WFH["\154\151\x63\145\156\163\x65\x54\171\x70\145"] = $G_WFH["\x6c\151\143\145\x6e\x73\x65\x53\x74\141\x74\x65"] = License::UNREACHABLE;
			goto bHNis;
			SHPAR: j5h80:
			goto zbc3A;
			dHl4C:
		}
	}

	private final function getFilePathFromSession()
	{
		goto RlsyZ;
		YYO5l:
		return $this->decrypt($RHfwu);
		goto lTxTn;
		RlsyZ:
		$RHfwu = $this->ADWzk->getSessionData($this->getSessionDataName());
		goto yNH_6;
		DBXvZ:
		return false;
		goto lY5Mr;
		yNH_6:
		if (isset($RHfwu)) {
			goto H_bqI;
		}
		goto DBXvZ;
		lY5Mr: H_bqI:
		goto YYO5l;
		lTxTn:
	}

	private final function storeFilePathInSession($RHfwu)
	{
		$this->ADWzk->setSessionData($this->getSessionDataName(), $this->encrypt($RHfwu));
	}

	private final function resetFailedAttempt()
	{
		goto yO0qJ;
		yO0qJ:
		$Kp5Lj = $this->getFilePathFromSession();
		goto C7hCg;
		X7x8W:
		unlink($Kp5Lj);
		goto aiEyf;
		C7hCg:
		if (!file_exists($Kp5Lj)) {
			goto fRpJy;
		}
		goto X7x8W;
		aiEyf: fRpJy:
		goto fn9X_;
		fn9X_:
	}

	private final function getFailedAttemptDate()
	{
		try {
			goto i3Xn0;
			vdaDh: CdHbG:
			goto lvOGQ;
			i3Xn0:
			$RHfwu = $this->getFilePathFromSession();
			goto Po0Go;
			TMBgw:
			return $this->decrypt(file_get_contents($RHfwu));
			goto vdaDh;
			Po0Go:
			if (!file_exists($RHfwu)) {
				goto CdHbG;
			}
			goto TMBgw;
			lvOGQ:
		} catch (\Exception $Qft5a) {
		}
		return null;
	}

	private final function failedAttempt()
	{
		goto e7qfa;
		e7qfa:
		$M02qi = date("\131\55\x6d\x2d\144");
		goto IdYwt;
		IdYwt:
		try {
			goto bXfkl;
			Q4GQL:
			if (is_dir($P6oTg)) {
				goto cyoOT;
			}
			goto zxD43;
			m6eP3: cyoOT:
			goto J6xAB;
			zxD43:
			$this->oiL3v->checkAndCreateFolder($P6oTg);
			goto m6eP3;
			J6xAB:
			@file_put_contents($this->getFilePathFromSession(), $this->encrypt($M02qi));
			goto NVQGV;
			bXfkl:
			$P6oTg = $this->olTXE->getPath(\Magento\Framework\App\Filesystem\DirectoryList::TMP);
			goto Q4GQL;
			NVQGV:
		} catch (\Exception $Qft5a) {
		}
		goto NdM09;
		NdM09:
		return $M02qi;
		goto p1oyy;
		p1oyy:
	}

	private final function storeInCache($Wy1wQ)
	{
		goto Lsf4w;
		N4WKV:
		$iWwlZ = $this->encrypt($H5yVG, $this->afFSD, $this->QNyHp);
		goto FwIU0;
		Lsf4w:
		$H5yVG = json_encode($Wy1wQ);
		goto N4WKV;
		FwIU0:
		$this->UhVTH->save($iWwlZ, $this->rkS3G);
		goto Nyt3b;
		Nyt3b:
	}

	public final function getDomain()
	{
		goto gCfz8;
		e7dwu:
		return $this->S8vKc;
		goto v4db1;
		di0iH: BbtYV:
		goto e7dwu;
		gCfz8:
		if (isset($this->S8vKc)) {
			goto BbtYV;
		}
		goto mRLsT;
		mRLsT:
		$this->S8vKc = parse_url($this->jcNg9->getBaseUrl(), PHP_URL_HOST);
		goto di0iH;
		v4db1:
	}

	public final function getEdition()
	{
		goto geX3b;
		NWNoo: bv1pp:
		goto Ach5Y;
		Ach5Y:
		return $this->{"\x65\x64\x69\164\x69\x6f\156"};
		goto IZ_k9;
		geX3b:
		if (isset($this->{"\145\x64\x69\x74\151\157\x6e"})) {
			goto bv1pp;
		}
		goto alyRp;
		alyRp:
		$this->{"\x65\144\x69\x74\151\157\x6e"} = $this->Y1vtz;
		goto NWNoo;
		IZ_k9:
	}

	public final function isActiveState()
	{
		return $this->getLicenseState() == License::ACTIVE_STATE;
	}

	public final function getLicenseState()
	{
		goto YSZah;
		YSZah:
		if (!(!isset($this->{"\154\x69\143\145\x6e\x73\145\123\164\x61\x74\x65"}) || !License::isValid())) {
			goto FzXfK;
		}
		goto KVQ2v;
		KVQ2v:
		return $this->A25uM;
		goto HNvtl;
		JjrwJ:
		return $this->{"\x6c\151\x63\x65\x6e\163\145\123\164\x61\x74\145"};
		goto GZf4b;
		HNvtl: FzXfK:
		goto JjrwJ;
		GZf4b:
	}

	public final function getLicenseType()
	{
		goto eAeMU;
		w1s8e: jePvf:
		goto EeNGT;
		EeNGT:
		return $this->{"\x6c\151\143\x65\156\163\x65\x54\171\160\x65"};
		goto Af11J;
		eAeMU:
		if (isset($this->{"\x6c\151\143\145\x6e\163\145\x54\x79\x70\145"})) {
			goto jePvf;
		}
		goto awCKC;
		awCKC:
		return $this->vAsKw;
		goto w1s8e;
		Af11J:
	}

	public static final function isValid()
	{
		return true;
	}

	public static final function getInstance()
	{
		goto Wuie4;
		CFWOx:
		return $FHaFx;
		goto KV4FF;
		whav9:
		$FHaFx = new License();
		goto Zz28T;
		x39UE:
		if (!($FHaFx === null)) {
			goto pWsht;
		}
		goto whav9;
		Zz28T: pWsht:
		goto CFWOx;
		Wuie4:
		static $FHaFx = null;
		goto x39UE;
		KV4FF:
	}

	public final function isInactiveState()
	{
		return $this->getLicenseState() == License::INACTIVE_STATE;
	}

	public final function isPendingState()
	{
		return $this->getLicenseState() == License::PENDING_STATE;
	}

	public final function isUnreachable()
	{
		return $this->getLicenseState() == License::UNREACHABLE;
	}

	public final function isUnreachableState()
	{
		return $this->isUnreachable();
	}

	public final function setProposalSent()
	{
		goto T3CHL;
		T3CHL:
		if (!($this->getEdition() == "\154\151\164\x65")) {
			goto ZE7pI;
		}
		goto EFolf;
		tplQz: ZE7pI:
		goto Zu5jJ;
		EFolf:
		\Cart2Quote\License\Http\Client::getInstance()->setProposalSent();
		goto Si14g;
		Si14g:
		$this->invalidateCache();
		goto tplQz;
		Zu5jJ:
	}

	public final function getProposalAmount()
	{
		return @$this->{"\160\x72\x6f\160\x6f\163\141\x6c\101\155\157\165\156\x74"};
	}

	private final function invalidateCache()
	{
		$this->UhVTH->remove($this->rkS3G);
	}

	public final function reload()
	{
		$this->invalidateCache();
		$this->init();
	}

	public function getExpiryDate()
	{
		return @$this->{"\x65\170\x70\151\162\171\104\141\164\x65"};
	}

	public function getOrderId()
	{
		return @$this->{"\x6f\162\144\145\x72\111\x64"};
	}

	public final function isAllowedForEdition($Ce2OE = "\x6f\x70\x65\156\163\x6f\x75\x72\x63\x65")
	{
		$qhlTQ = \Cart2Quote\License\Feature\Feature::getInstance($this);
		return $qhlTQ->isAllowedForEdition($Ce2OE);
	}
}