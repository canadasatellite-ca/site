<?php

namespace Magento\Amazon\Model\Amazon;

use Magento\Directory\Model\ResourceModel\Region;

class OrderRegionResolver
{
    /**
     * @var Region
     */
    private $regionResource;

    /**
     * Cached regions per country
     *
     * Pre-filled with some values that could help to resolve a code and save some DB queries
     * @var array
     */
    private $regions = [
        'US' => [
            'AL' => 'AL',
            'ALA' => 'AL',
            'ALA.' => 'AL',
            'ALABAMA' => 'AL',
            'AK' => 'AK',
            'ALASKA' => 'AK',
            'ARIZ' => 'AZ',
            'ARIZ.' => 'AZ',
            'ARIZONA' => 'AZ',
            'AZ' => 'AZ',
            'AR' => 'AR',
            'ARK' => 'AR',
            'ARK.' => 'AR',
            'ARKANSAS' => 'AR',
            'CA' => 'CA',
            'CALIF' => 'CA',
            'CALIF.' => 'CA',
            'CALIFORNIA' => 'CA',
            'CO' => 'CO',
            'COLO' => 'CO',
            'COLO.' => 'CO',
            'COLORADO' => 'CO',
            'CONN' => 'CT',
            'CONN.' => 'CT',
            'CONNECTICUT' => 'CT',
            'CT' => 'CT',
            'DE' => 'DE',
            'DEL' => 'DE',
            'DEL.' => 'DE',
            'DELAWARE' => 'DE',
            'D O C' => 'DC',
            'D-O-C' => 'DC',
            'D. O. C' => 'DC',
            'D.C' => 'DC',
            'D.C.' => 'DC',
            'D.O.C' => 'DC',
            'DC' => 'DC',
            'DISTRICT OF COLUMBIA' => 'DC',
            'DISTRICT-OF-COLUMBIA' => 'DC',
            'DISTRICT. OF. COLUMBIA' => 'DC',
            'DISTRICT. OF. COLUMBIA.' => 'DC',
            'DISTRICT.OF.COLUMBIA' => 'DC',
            'DISTRICT.OF.COLUMBIA.' => 'DC',
            'DOC' => 'DC',
            'FL' => 'FL',
            'FLA' => 'FL',
            'FLA.' => 'FL',
            'FLORIDA' => 'FL',
            'GA' => 'GA',
            'GA.' => 'GA',
            'GEORGIA' => 'GA',
            'HAWAII' => 'HI',
            'HI' => 'HI',
            'ID' => 'ID',
            'IDAHO' => 'ID',
            'IL' => 'IL',
            'ILL' => 'IL',
            'ILL.' => 'IL',
            'ILLINOIS' => 'IL',
            'IN' => 'IN',
            'IND' => 'IN',
            'IND.' => 'IN',
            'INDIANA' => 'IN',
            'IA' => 'IA',
            'IOWA' => 'IA',
            'KANS' => 'KS',
            'KANS.' => 'KS',
            'KANSAS' => 'KS',
            'KS' => 'KS',
            'KENTUCKY' => 'KY',
            'KY' => 'KY',
            'KY.' => 'KY',
            'LA' => 'LA',
            'LA.' => 'LA',
            'LOUISIANA' => 'LA',
            'MAINE' => 'ME',
            'ME' => 'ME',
            'MARYLAND' => 'MD',
            'MD' => 'MD',
            'MD.' => 'MD',
            'MA' => 'MA',
            'MASS' => 'MA',
            'MASS.' => 'MA',
            'MASSACHUSETTS' => 'MA',
            'MI' => 'MI',
            'MICH' => 'MI',
            'MICH.' => 'MI',
            'MICHIGAN' => 'MI',
            'MINN' => 'MN',
            'MINN.' => 'MN',
            'MINNESOTA' => 'MN',
            'MN' => 'MN',
            'MISS' => 'MS',
            'MISS.' => 'MS',
            'MISSISSIPPI' => 'MS',
            'MS' => 'MS',
            'MISSOURI' => 'MO',
            'MO' => 'MO',
            'MO.' => 'MO',
            'MONT' => 'MT',
            'MONT.' => 'MT',
            'MONTANA' => 'MT',
            'MT' => 'MT',
            'NE' => 'NE',
            'NEBR' => 'NE',
            'NEBR.' => 'NE',
            'NEBRASKA' => 'NE',
            'NEV' => 'NV',
            'NEV.' => 'NV',
            'NEVADA' => 'NV',
            'NV' => 'NV',
            'N H' => 'NH',
            'N-H' => 'NH',
            'N. H' => 'NH',
            'N.H' => 'NH',
            'N.H.' => 'NH',
            'NEW HAMPSHIRE' => 'NH',
            'NEW-HAMPSHIRE' => 'NH',
            'NEW. HAMPSHIRE' => 'NH',
            'NEW. HAMPSHIRE.' => 'NH',
            'NEW.HAMPSHIRE' => 'NH',
            'NEW.HAMPSHIRE.' => 'NH',
            'NH' => 'NH',
            'N J' => 'NJ',
            'N-J' => 'NJ',
            'N. J' => 'NJ',
            'N.J' => 'NJ',
            'N.J.' => 'NJ',
            'NEW JERSEY' => 'NJ',
            'NEW-JERSEY' => 'NJ',
            'NEW. JERSEY' => 'NJ',
            'NEW. JERSEY.' => 'NJ',
            'NEW.JERSEY' => 'NJ',
            'NEW.JERSEY.' => 'NJ',
            'NJ' => 'NJ',
            'N M' => 'NM',
            'N-M' => 'NM',
            'N. M' => 'NM',
            'N.M' => 'NM',
            'N.M.' => 'NM',
            'NEW MEXICO' => 'NM',
            'NEW-MEXICO' => 'NM',
            'NEW. MEXICO' => 'NM',
            'NEW. MEXICO.' => 'NM',
            'NEW.MEXICO' => 'NM',
            'NEW.MEXICO.' => 'NM',
            'NM' => 'NM',
            'N Y' => 'NY',
            'N-Y' => 'NY',
            'N. Y' => 'NY',
            'N.Y' => 'NY',
            'N.Y.' => 'NY',
            'NEW YORK' => 'NY',
            'NEW-YORK' => 'NY',
            'NEW. YORK' => 'NY',
            'NEW. YORK.' => 'NY',
            'NEW.YORK' => 'NY',
            'NEW.YORK.' => 'NY',
            'NY' => 'NY',
            'N C' => 'NC',
            'N-C' => 'NC',
            'N. C' => 'NC',
            'N.C' => 'NC',
            'N.C.' => 'NC',
            'NC' => 'NC',
            'NORTH CAROLINA' => 'NC',
            'NORTH-CAROLINA' => 'NC',
            'NORTH. CAROLINA' => 'NC',
            'NORTH. CAROLINA.' => 'NC',
            'NORTH.CAROLINA' => 'NC',
            'NORTH.CAROLINA.' => 'NC',
            'N D' => 'ND',
            'N-D' => 'ND',
            'N. D' => 'ND',
            'N.D' => 'ND',
            'N.D.' => 'ND',
            'ND' => 'ND',
            'NORTH DAKOTA' => 'ND',
            'NORTH-DAKOTA' => 'ND',
            'NORTH. DAKOTA' => 'ND',
            'NORTH. DAKOTA.' => 'ND',
            'NORTH.DAKOTA' => 'ND',
            'NORTH.DAKOTA.' => 'ND',
            'OH' => 'OH',
            'OHIO' => 'OH',
            'OK' => 'OK',
            'OKLA' => 'OK',
            'OKLA.' => 'OK',
            'OKLAHOMA' => 'OK',
            'OR' => 'OR',
            'ORE' => 'OR',
            'ORE.' => 'OR',
            'OREGON' => 'OR',
            'PA' => 'PA',
            'PA.' => 'PA',
            'PENNSYLVANIA' => 'PA',
            'R I' => 'RI',
            'R-I' => 'RI',
            'R. I' => 'RI',
            'R.I' => 'RI',
            'R.I.' => 'RI',
            'RHODE ISLAND' => 'RI',
            'RHODE-ISLAND' => 'RI',
            'RHODE. ISLAND' => 'RI',
            'RHODE. ISLAND.' => 'RI',
            'RHODE.ISLAND' => 'RI',
            'RHODE.ISLAND.' => 'RI',
            'RI' => 'RI',
            'S C' => 'SC',
            'S-C' => 'SC',
            'S. C' => 'SC',
            'S.C' => 'SC',
            'S.C.' => 'SC',
            'SC' => 'SC',
            'SOUTH CAROLINA' => 'SC',
            'SOUTH-CAROLINA' => 'SC',
            'SOUTH. CAROLINA' => 'SC',
            'SOUTH. CAROLINA.' => 'SC',
            'SOUTH.CAROLINA' => 'SC',
            'SOUTH.CAROLINA.' => 'SC',
            'S D' => 'SD',
            'S-D' => 'SD',
            'S. D' => 'SD',
            'S.D' => 'SD',
            'S.D.' => 'SD',
            'SD' => 'SD',
            'SOUTH DAKOTA' => 'SD',
            'SOUTH-DAKOTA' => 'SD',
            'SOUTH. DAKOTA' => 'SD',
            'SOUTH. DAKOTA.' => 'SD',
            'SOUTH.DAKOTA' => 'SD',
            'SOUTH.DAKOTA.' => 'SD',
            'TENN' => 'TN',
            'TENN.' => 'TN',
            'TENNESSEE' => 'TN',
            'TN' => 'TN',
            'TEX' => 'TX',
            'TEX.' => 'TX',
            'TEXAS' => 'TX',
            'TX' => 'TX',
            'UT' => 'UT',
            'UTAH' => 'UT',
            'VERMONT' => 'VT',
            'VT' => 'VT',
            'VT.' => 'VT',
            'VA' => 'VA',
            'VA.' => 'VA',
            'VIRGINIA' => 'VA',
            'WA' => 'WA',
            'WASH' => 'WA',
            'WASH.' => 'WA',
            'WASHINGTON' => 'WA',
            'W V' => 'WV',
            'W-V' => 'WV',
            'W. V' => 'WV',
            'W.V' => 'WV',
            'W.VA' => 'WV',
            'W.VA.' => 'WV',
            'WEST VIRGINIA' => 'WV',
            'WEST-VIRGINIA' => 'WV',
            'WEST. VIRGINIA' => 'WV',
            'WEST. VIRGINIA.' => 'WV',
            'WEST.VIRGINIA' => 'WV',
            'WEST.VIRGINIA.' => 'WV',
            'WV' => 'WV',
            'WI' => 'WI',
            'WIS' => 'WI',
            'WIS.' => 'WI',
            'WISCONSIN' => 'WI',
            'WY' => 'WY',
            'WYO' => 'WY',
            'WYO.' => 'WY',
            'WYOMING' => 'WY',
        ],
    ];

    /**
     * OrderRegionResolver constructor.
     * @param Region $regionResource
     */
    public function __construct(Region $regionResource)
    {
        $this->regionResource = $regionResource;
    }

    public function resolveRegion(string $countryCode, string $regionName): string
    {
        $countryCode = mb_strtoupper(trim($countryCode));
        $regionName = trim($regionName);
        $normalizedRegionName = mb_strtoupper($regionName);
        if (!isset($this->regions[$countryCode][$normalizedRegionName])) {
            $possibleNames = array_unique(
                [
                    $regionName,
                    // first letters capitalized for multi word names
                    implode(' ', array_map('ucfirst', explode(' ', mb_strtolower($regionName)))),
                    $normalizedRegionName,
                    mb_strtolower($regionName),
                ]
            );

            $adapter = $this->regionResource->getConnection();
            $select = $adapter->select();
            $select->from(
                ['region' => $adapter->getTableName('directory_country_region')],
                ['code']
            );
            $select->joinLeft(
                ['region_name' => $adapter->getTableName('directory_country_region_name')],
                'region.region_id = region_name.region_id',
                []
            );
            $select->where('region.country_id = ?', $countryCode);

            $nameLookupConditions = [
                $adapter->quoteInto('region.code = ?', mb_strtoupper($regionName)),
            ];
            foreach ($possibleNames as $possibleName) {
                $nameLookupConditions[] = $adapter->quoteInto('region.default_name = ?', $possibleName);
                $nameLookupConditions[] = $adapter->quoteInto('region_name.name = ?', $possibleName);
            }
            $select->where(implode(' OR ', $nameLookupConditions));
            $select->limit(1);
            $regionCode = $adapter->fetchOne($select);

            $resolvedRegion = $regionCode ?: $regionName;
            if ($regionCode) {
                $this->regions[$countryCode][$regionCode] = $resolvedRegion;
            }
            $this->regions[$countryCode][$normalizedRegionName] = $resolvedRegion;
        }
        return $this->regions[$countryCode][$normalizedRegionName];
    }
}
