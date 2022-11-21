<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Constant;

/**
 * Class Code
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-05-25)
 * @package App\Constant
 *
 * @see http://www.geonames.org/export/codes.html
 */
class Code
{
    /* Feature classes */
    public const FEATURE_CLASS_A = 'A'; /* country, state, region,... */
    public const FEATURE_CLASS_H = 'H'; /* stream, lake, ... */
    public const FEATURE_CLASS_L = 'L'; /* parks, area, ... */
    public const FEATURE_CLASS_P = 'P'; /* city, village, ... */
    public const FEATURE_CLASS_R = 'R'; /* road, railroad, ... */
    public const FEATURE_CLASS_S = 'S'; /* spot, building, farm, ... */
    public const FEATURE_CLASS_T = 'T'; /* mountain, hill, rock,... */
    public const FEATURE_CLASS_U = 'U'; /* undersea */
    public const FEATURE_CLASS_V = 'V'; /* forest, heath, ... */

    /* Feature classes */
    public const FEATURE_CLASSES_ALL = [
        self::FEATURE_CLASS_A,
        self::FEATURE_CLASS_H,
        self::FEATURE_CLASS_L,
        self::FEATURE_CLASS_P,
        self::FEATURE_CLASS_R,
        self::FEATURE_CLASS_S,
        self::FEATURE_CLASS_T,
        self::FEATURE_CLASS_U,
        self::FEATURE_CLASS_V,
    ];

    /* Feature codes */

    /* A → country, state, region, ... */
    public const FEATURE_CODE_A_ADM1 = 'ADM1'; /* first-order administrative division	a primary administrative division of a country, such as a state in the United States */
    public const FEATURE_CODE_A_ADM1H = 'ADM1H'; /* historical first-order administrative division	a former first-order administrative division */
    public const FEATURE_CODE_A_ADM2 = 'ADM2'; /* second-order administrative division	a subdivision of a first-order administrative division */
    public const FEATURE_CODE_A_ADM2H = 'ADM2H'; /* historical second-order administrative division	a former second-order administrative division */
    public const FEATURE_CODE_A_ADM3 = 'ADM3'; /* third-order administrative division	a subdivision of a second-order administrative division */
    public const FEATURE_CODE_A_ADM3H = 'ADM3H'; /* historical third-order administrative division	a former third-order administrative division */
    public const FEATURE_CODE_A_ADM4 = 'ADM4'; /* fourth-order administrative division	a subdivision of a third-order administrative division */
    public const FEATURE_CODE_A_ADM4H = 'ADM4H'; /* historical fourth-order administrative division	a former fourth-order administrative division */
    public const FEATURE_CODE_A_ADM5 = 'ADM5'; /* fifth-order administrative division	a subdivision of a fourth-order administrative division */
    public const FEATURE_CODE_A_ADM5H = 'ADM5H'; /* historical fifth-order administrative division	a former fifth-order administrative division */
    public const FEATURE_CODE_A_ADMD = 'ADMD'; /* administrative division	an administrative division of a country, undifferentiated as to administrative level */
    public const FEATURE_CODE_A_ADMDH = 'ADMDH'; /* historical administrative division	a former administrative division of a political entity, undifferentiated as to administrative level */
    public const FEATURE_CODE_A_LTER = 'LTER'; /* leased area	a tract of land leased to another country, usually for military installations */
    public const FEATURE_CODE_A_PCL = 'PCL'; /* political entity */
    public const FEATURE_CODE_A_PCLD = 'PCLD'; /* dependent political entity */
    public const FEATURE_CODE_A_PCLF = 'PCLF'; /* freely associated state */
    public const FEATURE_CODE_A_PCLH = 'PCLH'; /* historical political entity	a former political entity */
    public const FEATURE_CODE_A_PCLI = 'PCLI'; /* independent political entity */
    public const FEATURE_CODE_A_PCLIX = 'PCLIX'; /* section of independent political entity */
    public const FEATURE_CODE_A_PCLS = 'PCLS'; /* semi-independent political entity */
    public const FEATURE_CODE_A_PRSH = 'PRSH'; /* parish	an ecclesiastical district */
    public const FEATURE_CODE_A_TERR = 'TERR'; /* territory */
    public const FEATURE_CODE_A_ZN = 'ZN'; /* zone */
    public const FEATURE_CODE_A_ZNB = 'ZNB'; /* buffer zone	a zone recognized as a buffer between two nations in which military presence is minimal or absent */

    /* */
    public const FEATURE_CODE_H_AIRS = 'AIRS'; /* seaplane landing area	a place on a waterbody where floatplanes land and take off */
    public const FEATURE_CODE_H_ANCH = 'ANCH'; /* anchorage	an area where vessels may anchor */
    public const FEATURE_CODE_H_BAY = 'BAY'; /* bay	a coastal indentation between two capes or headlands, larger than a cove but smaller than a gulf */
    public const FEATURE_CODE_H_BAYS = 'BAYS'; /* bays	coastal indentations between two capes or headlands, larger than a cove but smaller than a gulf */
    public const FEATURE_CODE_H_BGHT = 'BGHT'; /* bight(s)	an open body of water forming a slight recession in a coastline */
    public const FEATURE_CODE_H_BNK = 'BNK'; /* bank(s)	an elevation, typically located on a shelf, over which the depth of water is relatively shallow but sufficient for most surface navigation */
    public const FEATURE_CODE_H_BNKR = 'BNKR'; /* stream bank	a sloping margin of a stream channel which normally confines the stream to its channel on land */
    public const FEATURE_CODE_H_BNKX = 'BNKX'; /* section of bank */
    public const FEATURE_CODE_H_BOG = 'BOG'; /* bog(s)	a wetland characterized by peat forming sphagnum moss, sedge, and other acid-water plants */
    public const FEATURE_CODE_H_CAPG = 'CAPG'; /* icecap	a dome-shaped mass of glacial ice covering an area of mountain summits or other high lands; smaller than an ice sheet */
    public const FEATURE_CODE_H_CHN = 'CHN'; /* channel	the deepest part of a stream, bay, lagoon, or strait, through which the main current flows */
    public const FEATURE_CODE_H_CHNL = 'CHNL'; /* lake channel(s)	that part of a lake having water deep enough for navigation between islands, shoals, etc. */
    public const FEATURE_CODE_H_CHNM = 'CHNM'; /* marine channel	that part of a body of water deep enough for navigation through an area otherwise not suitable */
    public const FEATURE_CODE_H_CHNN = 'CHNN'; /* navigation channel	a buoyed channel of sufficient depth for the safe navigation of vessels */
    public const FEATURE_CODE_H_CNFL = 'CNFL'; /* confluence	a place where two or more streams or intermittent streams flow together */
    public const FEATURE_CODE_H_CNL = 'CNL'; /* canal	an artificial watercourse */
    public const FEATURE_CODE_H_CNLA = 'CNLA'; /* aqueduct	a conduit used to carry water */
    public const FEATURE_CODE_H_CNLB = 'CNLB'; /* canal bend	a conspicuously curved or bent section of a canal */
    public const FEATURE_CODE_H_CNLD = 'CNLD'; /* drainage canal	an artificial waterway carrying water away from a wetland or from drainage ditches */
    public const FEATURE_CODE_H_CNLI = 'CNLI'; /* irrigation canal	a canal which serves as a main conduit for irrigation water */
    public const FEATURE_CODE_H_CNLN = 'CNLN'; /* navigation canal(s)	a watercourse constructed for navigation of vessels */
    public const FEATURE_CODE_H_CNLQ = 'CNLQ'; /* abandoned canal */
    public const FEATURE_CODE_H_CNLSB = 'CNLSB'; /* underground irrigation canal(s)	a gently inclined underground tunnel bringing water for irrigation from aquifers */
    public const FEATURE_CODE_H_CNLX = 'CNLX'; /* section of canal */
    public const FEATURE_CODE_H_COVE = 'COVE'; /* cove(s)	a small coastal indentation, smaller than a bay */
    public const FEATURE_CODE_H_CRKT = 'CRKT'; /* tidal creek(s)	a meandering channel in a coastal wetland subject to bi-directional tidal currents */
    public const FEATURE_CODE_H_CRNT = 'CRNT'; /* current	a horizontal flow of water in a given direction with uniform velocity */
    public const FEATURE_CODE_H_CUTF = 'CUTF'; /* cutoff	a channel formed as a result of a stream cutting through a meander neck */
    public const FEATURE_CODE_H_DCK = 'DCK'; /* dock(s)	a waterway between two piers, or cut into the land for the berthing of ships */
    public const FEATURE_CODE_H_DCKB = 'DCKB'; /* docking basin	a part of a harbor where ships dock */
    public const FEATURE_CODE_H_DOMG = 'DOMG'; /* icecap dome	a comparatively elevated area on an icecap */
    public const FEATURE_CODE_H_DPRG = 'DPRG'; /* icecap depression	a comparatively depressed area on an icecap */
    public const FEATURE_CODE_H_DTCH = 'DTCH'; /* ditch	a small artificial watercourse dug for draining or irrigating the land */
    public const FEATURE_CODE_H_DTCHD = 'DTCHD'; /* drainage ditch	a ditch which serves to drain the land */
    public const FEATURE_CODE_H_DTCHI = 'DTCHI'; /* irrigation ditch	a ditch which serves to distribute irrigation water */
    public const FEATURE_CODE_H_DTCHM = 'DTCHM'; /* ditch mouth(s)	an area where a drainage ditch enters a lagoon, lake or bay */
    public const FEATURE_CODE_H_ESTY = 'ESTY'; /* estuary	a funnel-shaped stream mouth or embayment where fresh water mixes with sea water under tidal influences */
    public const FEATURE_CODE_H_FISH = 'FISH'; /* fishing area	a fishing ground, bank or area where fishermen go to catch fish */
    public const FEATURE_CODE_H_FJD = 'FJD'; /* fjord	a long, narrow, steep-walled, deep-water arm of the sea at high latitudes, usually along mountainous coasts */
    public const FEATURE_CODE_H_FJDS = 'FJDS'; /* fjords	long, narrow, steep-walled, deep-water arms of the sea at high latitudes, usually along mountainous coasts */
    public const FEATURE_CODE_H_FLLS = 'FLLS'; /* waterfall(s)	a perpendicular or very steep descent of the water of a stream */
    public const FEATURE_CODE_H_FLLSX = 'FLLSX'; /* section of waterfall(s) */
    public const FEATURE_CODE_H_FLTM = 'FLTM'; /* mud flat(s)	a relatively level area of mud either between high and low tide lines, or subject to flooding */
    public const FEATURE_CODE_H_FLTT = 'FLTT'; /* tidal flat(s)	a large flat area of mud or sand attached to the shore and alternately covered and uncovered by the tide */
    public const FEATURE_CODE_H_GLCR = 'GLCR'; /* glacier(s)	a mass of ice, usually at high latitudes or high elevations, with sufficient thickness to flow away from the source area in lobes, tongues, or masses */
    public const FEATURE_CODE_H_GULF = 'GULF'; /* gulf	a large recess in the coastline, larger than a bay */
    public const FEATURE_CODE_H_GYSR = 'GYSR'; /* geyser	a type of hot spring with intermittent eruptions of jets of hot water and steam */
    public const FEATURE_CODE_H_HBR = 'HBR'; /* harbor(s)	a haven or space of deep water so sheltered by the adjacent land as to afford a safe anchorage for ships */
    public const FEATURE_CODE_H_HBRX = 'HBRX'; /* section of harbor */
    public const FEATURE_CODE_H_INLT = 'INLT'; /* inlet	a narrow waterway extending into the land, or connecting a bay or lagoon with a larger body of water */
    public const FEATURE_CODE_H_INLTQ = 'INLTQ'; /* former inlet	an inlet which has been filled in, or blocked by deposits */
    public const FEATURE_CODE_H_LBED = 'LBED'; /* lake bed(s)	a dried up or drained area of a former lake */
    public const FEATURE_CODE_H_LGN = 'LGN'; /* lagoon	a shallow coastal waterbody, completely or partly separated from a larger body of water by a barrier island, coral reef or other depositional feature */
    public const FEATURE_CODE_H_LGNS = 'LGNS'; /* lagoons	shallow coastal waterbodies, completely or partly separated from a larger body of water by a barrier island, coral reef or other depositional feature */
    public const FEATURE_CODE_H_LGNX = 'LGNX'; /* section of lagoon */
    public const FEATURE_CODE_H_LK = 'LK'; /* lake	a large inland body of standing water */
    public const FEATURE_CODE_H_LKC = 'LKC'; /* crater lake	a lake in a crater or caldera */
    public const FEATURE_CODE_H_LKI = 'LKI'; /* intermittent lake */
    public const FEATURE_CODE_H_LKN = 'LKN'; /* salt lake	an inland body of salt water with no outlet */
    public const FEATURE_CODE_H_LKNI = 'LKNI'; /* intermittent salt lake */
    public const FEATURE_CODE_H_LKO = 'LKO'; /* oxbow lake	a crescent-shaped lake commonly found adjacent to meandering streams */
    public const FEATURE_CODE_H_LKOI = 'LKOI'; /* intermittent oxbow lake */
    public const FEATURE_CODE_H_LKS = 'LKS'; /* lakes	large inland bodies of standing water */
    public const FEATURE_CODE_H_LKSB = 'LKSB'; /* underground lake	a standing body of water in a cave */
    public const FEATURE_CODE_H_LKSC = 'LKSC'; /* crater lakes	lakes in a crater or caldera */
    public const FEATURE_CODE_H_LKSI = 'LKSI'; /* intermittent lakes */
    public const FEATURE_CODE_H_LKSN = 'LKSN'; /* salt lakes	inland bodies of salt water with no outlet */
    public const FEATURE_CODE_H_LKSNI = 'LKSNI'; /* intermittent salt lakes */
    public const FEATURE_CODE_H_LKX = 'LKX'; /* section of lake */
    public const FEATURE_CODE_H_MFGN = 'MFGN'; /* salt evaporation ponds	diked salt ponds used in the production of solar evaporated salt */
    public const FEATURE_CODE_H_MGV = 'MGV'; /* mangrove swamp	a tropical tidal mud flat characterized by mangrove vegetation */
    public const FEATURE_CODE_H_MOOR = 'MOOR'; /* moor(s)	an area of open ground overlaid with wet peaty soils */
    public const FEATURE_CODE_H_MRSH = 'MRSH'; /* marsh(es)	a wetland dominated by grass-like vegetation */
    public const FEATURE_CODE_H_MRSHN = 'MRSHN'; /* salt marsh	a flat area, subject to periodic salt water inundation, dominated by grassy salt-tolerant plants */
    public const FEATURE_CODE_H_NRWS = 'NRWS'; /* narrows	a navigable narrow part of a bay, strait, river, etc. */
    public const FEATURE_CODE_H_OCN = 'OCN'; /* ocean	one of the major divisions of the vast expanse of salt water covering part of the earth */
    public const FEATURE_CODE_H_OVF = 'OVF'; /* overfalls	an area of breaking waves caused by the meeting of currents or by waves moving against the current */
    public const FEATURE_CODE_H_PND = 'PND'; /* pond	a small standing waterbody */
    public const FEATURE_CODE_H_PNDI = 'PNDI'; /* intermittent pond */
    public const FEATURE_CODE_H_PNDN = 'PNDN'; /* salt pond	a small standing body of salt water often in a marsh or swamp, usually along a seacoast */
    public const FEATURE_CODE_H_PNDNI = 'PNDNI'; /* intermittent salt pond(s) */
    public const FEATURE_CODE_H_PNDS = 'PNDS'; /* ponds	small standing waterbodies */
    public const FEATURE_CODE_H_PNDSF = 'PNDSF'; /* fishponds	ponds or enclosures in which fish are kept or raised */
    public const FEATURE_CODE_H_PNDSI = 'PNDSI'; /* intermittent ponds */
    public const FEATURE_CODE_H_PNDSN = 'PNDSN'; /* salt ponds	small standing bodies of salt water often in a marsh or swamp, usually along a seacoast */
    public const FEATURE_CODE_H_POOL = 'POOL'; /* pool(s)	a small and comparatively still, deep part of a larger body of water such as a stream or harbor; or a small body of standing water */
    public const FEATURE_CODE_H_POOLI = 'POOLI'; /* intermittent pool */
    public const FEATURE_CODE_H_RCH = 'RCH'; /* reach	a straight section of a navigable stream or channel between two bends */
    public const FEATURE_CODE_H_RDGG = 'RDGG'; /* icecap ridge	a linear elevation on an icecap */
    public const FEATURE_CODE_H_RDST = 'RDST'; /* roadstead	an open anchorage affording less protection than a harbor */
    public const FEATURE_CODE_H_RF = 'RF'; /* reef(s)	a surface-navigation hazard composed of consolidated material */
    public const FEATURE_CODE_H_RFC = 'RFC'; /* coral reef(s)	a surface-navigation hazard composed of coral */
    public const FEATURE_CODE_H_RFX = 'RFX'; /* section of reef */
    public const FEATURE_CODE_H_RPDS = 'RPDS'; /* rapids	a turbulent section of a stream associated with a steep, irregular stream bed */
    public const FEATURE_CODE_H_RSV = 'RSV'; /* reservoir(s)	an artificial pond or lake */
    public const FEATURE_CODE_H_RSVI = 'RSVI'; /* intermittent reservoir */
    public const FEATURE_CODE_H_RSVT = 'RSVT'; /* water tank	a contained pool or tank of water at, below, or above ground level */
    public const FEATURE_CODE_H_RVN = 'RVN'; /* ravine(s)	a small, narrow, deep, steep-sided stream channel, smaller than a gorge */
    public const FEATURE_CODE_H_SBKH = 'SBKH'; /* sabkha(s)	a salt flat or salt encrusted plain subject to periodic inundation from flooding or high tides */
    public const FEATURE_CODE_H_SD = 'SD'; /* sound	a long arm of the sea forming a channel between the mainland and an island or islands; or connecting two larger bodies of water */
    public const FEATURE_CODE_H_SEA = 'SEA'; /* sea	a large body of salt water more or less confined by continuous land or chains of islands forming a subdivision of an ocean */
    public const FEATURE_CODE_H_SHOL = 'SHOL'; /* shoal(s)	a surface-navigation hazard composed of unconsolidated material */
    public const FEATURE_CODE_H_SILL = 'SILL'; /* sill	the low part of an underwater gap or saddle separating basins, including a similar feature at the mouth of a fjord */
    public const FEATURE_CODE_H_SPNG = 'SPNG'; /* spring(s)	a place where ground water flows naturally out of the ground */
    public const FEATURE_CODE_H_SPNS = 'SPNS'; /* sulphur spring(s)	a place where sulphur ground water flows naturally out of the ground */
    public const FEATURE_CODE_H_SPNT = 'SPNT'; /* hot spring(s)	a place where hot ground water flows naturally out of the ground */
    public const FEATURE_CODE_H_STM = 'STM'; /* stream	a body of running water moving to a lower level in a channel on land */
    public const FEATURE_CODE_H_STMA = 'STMA'; /* anabranch	a diverging branch flowing out of a main stream and rejoining it downstream */
    public const FEATURE_CODE_H_STMB = 'STMB'; /* stream bend	a conspicuously curved or bent segment of a stream */
    public const FEATURE_CODE_H_STMC = 'STMC'; /* canalized stream	a stream that has been substantially ditched, diked, or straightened */
    public const FEATURE_CODE_H_STMD = 'STMD'; /* distributary(-ies)	a branch which flows away from the main stream, as in a delta or irrigation canal */
    public const FEATURE_CODE_H_STMH = 'STMH'; /* headwaters	the source and upper part of a stream, including the upper drainage basin */
    public const FEATURE_CODE_H_STMI = 'STMI'; /* intermittent stream */
    public const FEATURE_CODE_H_STMIX = 'STMIX'; /* section of intermittent stream */
    public const FEATURE_CODE_H_STMM = 'STMM'; /* stream mouth(s)	a place where a stream discharges into a lagoon, lake, or the sea */
    public const FEATURE_CODE_H_STMQ = 'STMQ'; /* abandoned watercourse	a former stream or distributary no longer carrying flowing water, but still evident due to lakes, wetland, topographic or vegetation patterns */
    public const FEATURE_CODE_H_STMS = 'STMS'; /* streams	bodies of running water moving to a lower level in a channel on land */
    public const FEATURE_CODE_H_STMSB = 'STMSB'; /* lost river	a surface stream that disappears into an underground channel, or dries up in an arid area */
    public const FEATURE_CODE_H_STMX = 'STMX'; /* section of stream */
    public const FEATURE_CODE_H_STRT = 'STRT'; /* strait	a relatively narrow waterway, usually narrower and less extensive than a sound, connecting two larger bodies of water */
    public const FEATURE_CODE_H_SWMP = 'SWMP'; /* swamp	a wetland dominated by tree vegetation */
    public const FEATURE_CODE_H_SYSI = 'SYSI'; /* irrigation system	a network of ditches and one or more of the following elements: water supply, reservoir, canal, pump, well, drain, etc. */
    public const FEATURE_CODE_H_TNLC = 'TNLC'; /* canal tunnel	a tunnel through which a canal passes */
    public const FEATURE_CODE_H_WAD = 'WAD'; /* wadi	a valley or ravine, bounded by relatively steep banks, which in the rainy season becomes a watercourse; found primarily in North Africa and the Middle East */
    public const FEATURE_CODE_H_WADB = 'WADB'; /* wadi bend	a conspicuously curved or bent segment of a wadi */
    public const FEATURE_CODE_H_WADJ = 'WADJ'; /* wadi junction	a place where two or more wadies join */
    public const FEATURE_CODE_H_WADM = 'WADM'; /* wadi mouth	the lower terminus of a wadi where it widens into an adjoining floodplain, depression, or waterbody */
    public const FEATURE_CODE_H_WADS = 'WADS'; /* wadies	valleys or ravines, bounded by relatively steep banks, which in the rainy season become watercourses; found primarily in North Africa and the Middle East */
    public const FEATURE_CODE_H_WADX = 'WADX'; /* section of wadi */
    public const FEATURE_CODE_H_WHRL = 'WHRL'; /* whirlpool	a turbulent, rotating movement of water in a stream */
    public const FEATURE_CODE_H_WLL = 'WLL'; /* well	a cylindrical hole, pit, or tunnel drilled or dug down to a depth from which water, oil, or gas can be pumped or brought to the surface */
    public const FEATURE_CODE_H_WLLQ = 'WLLQ'; /* abandoned well */
    public const FEATURE_CODE_H_WLLS = 'WLLS'; /* wells	cylindrical holes, pits, or tunnels drilled or dug down to a depth from which water, oil, or gas can be pumped or brought to the surface */
    public const FEATURE_CODE_H_WTLD = 'WTLD'; /* wetland	an area subject to inundation, usually characterized by bog, marsh, or swamp vegetation */
    public const FEATURE_CODE_H_WTLDI = 'WTLDI'; /* intermittent wetland */
    public const FEATURE_CODE_H_WTRC = 'WTRC'; /* watercourse	a natural, well-defined channel produced by flowing water, or an artificial channel designed to carry flowing water */
    public const FEATURE_CODE_H_WTRH = 'WTRH'; /* waterhole(s)	a natural hole, hollow, or small depression that contains water, used by man and animals, especially in arid areas */

    /* */
    public const FEATURE_CODE_L_AGRC = 'AGRC'; /* agricultural colony	a tract of land set aside for agricultural settlement */
    public const FEATURE_CODE_L_AMUS = 'AMUS'; /* amusement park	Amusement Park are theme parks, adventure parks offering entertainment, similar to funfairs but with a fix location */
    public const FEATURE_CODE_L_AREA = 'AREA'; /* area	a tract of land without homogeneous character or boundaries */
    public const FEATURE_CODE_L_BSND = 'BSND'; /* drainage basin	an area drained by a stream */
    public const FEATURE_CODE_L_BSNP = 'BSNP'; /* petroleum basin	an area underlain by an oil-rich structural basin */
    public const FEATURE_CODE_L_BTL = 'BTL'; /* battlefield	a site of a land battle of historical importance */
    public const FEATURE_CODE_L_CLG = 'CLG'; /* clearing	an area in a forest with trees removed */
    public const FEATURE_CODE_L_CMN = 'CMN'; /* common	a park or pasture for community use */
    public const FEATURE_CODE_L_CNS = 'CNS'; /* concession area	a lease of land by a government for economic development, e.g., mining, forestry */
    public const FEATURE_CODE_L_COLF = 'COLF'; /* coalfield	a region in which coal deposits of possible economic value occur */
    public const FEATURE_CODE_L_CONT = 'CONT'; /* continent	continent: Europe, Africa, Asia, North America, South America, Oceania, Antarctica */
    public const FEATURE_CODE_L_CST = 'CST'; /* coast	a zone of variable width straddling the shoreline */
    public const FEATURE_CODE_L_CTRB = 'CTRB'; /* business center	a place where a number of businesses are located */
    public const FEATURE_CODE_L_DEVH = 'DEVH'; /* housing development	a tract of land on which many houses of similar design are built according to a development plan */
    public const FEATURE_CODE_L_FLD = 'FLD'; /* field(s)	an open as opposed to wooded area */
    public const FEATURE_CODE_L_FLDI = 'FLDI'; /* irrigated field(s)	a tract of level or terraced land which is irrigated */
    public const FEATURE_CODE_L_GASF = 'GASF'; /* gasfield	an area containing a subterranean store of natural gas of economic value */
    public const FEATURE_CODE_L_GRAZ = 'GRAZ'; /* grazing area	an area of grasses and shrubs used for grazing */
    public const FEATURE_CODE_L_GVL = 'GVL'; /* gravel area	an area covered with gravel */
    public const FEATURE_CODE_L_INDS = 'INDS'; /* industrial area	an area characterized by industrial activity */
    public const FEATURE_CODE_L_LAND = 'LAND'; /* arctic land	a tract of land in the Arctic */
    public const FEATURE_CODE_L_LCTY = 'LCTY'; /* locality	a minor area or place of unspecified or mixed character and indefinite boundaries */
    public const FEATURE_CODE_L_MILB = 'MILB'; /* military base	a place used by an army or other armed service for storing arms and supplies, and for accommodating and training troops, a base from which operations can be initiated */
    public const FEATURE_CODE_L_MNA = 'MNA'; /* mining area	an area of mine sites where minerals and ores are extracted */
    public const FEATURE_CODE_L_MVA = 'MVA'; /* maneuver area	a tract of land where military field exercises are carried out */
    public const FEATURE_CODE_L_NVB = 'NVB'; /* naval base	an area used to store supplies, provide barracks for troops and naval personnel, a port for naval vessels, and from which operations are initiated */
    public const FEATURE_CODE_L_OAS = 'OAS'; /* oasis(-es)	an area in a desert made productive by the availability of water */
    public const FEATURE_CODE_L_OILF = 'OILF'; /* oilfield	an area containing a subterranean store of petroleum of economic value */
    public const FEATURE_CODE_L_PEAT = 'PEAT'; /* peat cutting area	an area where peat is harvested */
    public const FEATURE_CODE_L_PRK = 'PRK'; /* park	an area, often of forested land, maintained as a place of beauty, or for recreation */
    public const FEATURE_CODE_L_PRT = 'PRT'; /* port	a place provided with terminal and transfer facilities for loading and discharging waterborne cargo or passengers, usually located in a harbor */
    public const FEATURE_CODE_L_QCKS = 'QCKS'; /* quicksand	an area where loose sand with water moving through it may become unstable when heavy objects are placed at the surface, causing them to sink */
    public const FEATURE_CODE_L_RES = 'RES'; /* reserve	a tract of public land reserved for future use or restricted as to use */
    public const FEATURE_CODE_L_RESA = 'RESA'; /* agricultural reserve	a tract of land reserved for agricultural reclamation and/or development */
    public const FEATURE_CODE_L_RESF = 'RESF'; /* forest reserve	a forested area set aside for preservation or controlled use */
    public const FEATURE_CODE_L_RESH = 'RESH'; /* hunting reserve	a tract of land used primarily for hunting */
    public const FEATURE_CODE_L_RESN = 'RESN'; /* nature reserve	an area reserved for the maintenance of a natural habitat */
    public const FEATURE_CODE_L_RESP = 'RESP'; /* palm tree reserve	an area of palm trees where use is controlled */
    public const FEATURE_CODE_L_RESV = 'RESV'; /* reservation	a tract of land set aside for aboriginal, tribal, or native populations */
    public const FEATURE_CODE_L_RESW = 'RESW'; /* wildlife reserve	a tract of public land reserved for the preservation of wildlife */
    public const FEATURE_CODE_L_RGN = 'RGN'; /* region	an area distinguished by one or more observable physical or cultural characteristics */
    public const FEATURE_CODE_L_RGNE = 'RGNE'; /* economic region	a region of a country established for economic development or for statistical purposes */
    public const FEATURE_CODE_L_RGNH = 'RGNH'; /* historical region	a former historic area distinguished by one or more observable physical or cultural characteristics */
    public const FEATURE_CODE_L_RGNL = 'RGNL'; /* lake region	a tract of land distinguished by numerous lakes */
    public const FEATURE_CODE_L_RNGA = 'RNGA'; /* artillery range	a tract of land used for artillery firing practice */
    public const FEATURE_CODE_L_SALT = 'SALT'; /* salt area	a shallow basin or flat where salt accumulates after periodic inundation */
    public const FEATURE_CODE_L_SNOW = 'SNOW'; /* snowfield	an area of permanent snow and ice forming the accumulation area of a glacier */
    public const FEATURE_CODE_L_TRB = 'TRB'; /* tribal area	a tract of land used by nomadic or other tribes */

    /* P → city, village, ... */
    public const FEATURE_CODE_P_PPL   = 'PPL';   /* populated place; a city, town, village, or other agglomeration of buildings where people live and work */
    public const FEATURE_CODE_P_PPLA  = 'PPLA';  /* seat of a first-order administrative division; seat of a first-order administrative division (PPLC takes precedence over PPLA) */
    public const FEATURE_CODE_P_PPLA2 = 'PPLA2'; /* seat of a second-order administrative division */
    public const FEATURE_CODE_P_PPLA3 = 'PPLA3'; /* seat of a third-order administrative division */
    public const FEATURE_CODE_P_PPLA4 = 'PPLA4'; /* seat of a fourth-order administrative division */
    public const FEATURE_CODE_P_PPLA5 = 'PPLA5'; /* seat of a fifth-order administrative division */
    public const FEATURE_CODE_P_PPLC  = 'PPLC';  /* PPLC; capital of a political entity */
    public const FEATURE_CODE_P_PPLCH = 'PPLCH'; /* historical capital of a political entity; a former capital of a political entity */
    public const FEATURE_CODE_P_PPLF  = 'PPLF';  /* farm village; a populated place where the population is largely engaged in agricultural activities */
    public const FEATURE_CODE_P_PPLG  = 'PPLG';  /* seat of government of a political entity */
    public const FEATURE_CODE_P_PPLH  = 'PPLH';  /* historical populated place; a populated place that no longer exists */
    public const FEATURE_CODE_P_PPLL  = 'PPLL';  /* populated locality; an area similar to a locality but with a small group of dwellings or other buildings */
    public const FEATURE_CODE_P_PPLQ  = 'PPLQ';  /* abandoned populated place */
    public const FEATURE_CODE_P_PPLR  = 'PPLR';  /* religious populated place; a populated place whose population is largely engaged in religious occupations */
    public const FEATURE_CODE_P_PPLS  = 'PPLS';  /* populated places; cities, towns, villages, or other agglomerations of buildings where people live and work */
    public const FEATURE_CODE_P_PPLW  = 'PPLW';  /* destroyed populated place; a village, town or city destroyed by a natural disaster, or by war */
    public const FEATURE_CODE_P_PPLX  = 'PPLX';  /* section of populated place */
    public const FEATURE_CODE_P_STLMT = 'STLMT'; /* israeli settlement */

    /* */
    public const FEATURE_CODE_R_CSWY = 'CSWY'; /* causeway	a raised roadway across wet ground or shallow water */
    public const FEATURE_CODE_R_OILP = 'OILP'; /* oil pipeline	a pipeline used for transporting oil */
    public const FEATURE_CODE_R_PRMN = 'PRMN'; /* promenade	a place for public walking, usually along a beach front */
    public const FEATURE_CODE_R_PTGE = 'PTGE'; /* portage	a place where boats, goods, etc., are carried overland between navigable waters */
    public const FEATURE_CODE_R_RD = 'RD'; /* road	an open way with improved surface for transportation of animals, people and vehicles */
    public const FEATURE_CODE_R_RDA = 'RDA'; /* ancient road	the remains of a road used by ancient cultures */
    public const FEATURE_CODE_R_RDB = 'RDB'; /* road bend	a conspicuously curved or bent section of a road */
    public const FEATURE_CODE_R_RDCUT = 'RDCUT'; /* road cut	an excavation cut through a hill or ridge for a road */
    public const FEATURE_CODE_R_RDJCT = 'RDJCT'; /* road junction	a place where two or more roads join */
    public const FEATURE_CODE_R_RJCT = 'RJCT'; /* railroad junction	a place where two or more railroad tracks join */
    public const FEATURE_CODE_R_RR = 'RR'; /* railroad	a permanent twin steel-rail track on which freight and passenger cars move long distances */
    public const FEATURE_CODE_R_RRQ = 'RRQ'; /* abandoned railroad	 */
    public const FEATURE_CODE_R_RTE = 'RTE'; /* caravan route	the route taken by caravans */
    public const FEATURE_CODE_R_RYD = 'RYD'; /* railroad yard	a system of tracks used for the making up of trains, and switching and storing freight cars */
    public const FEATURE_CODE_R_ST = 'ST'; /* street	a paved urban thoroughfare */
    public const FEATURE_CODE_R_STKR = 'STKR'; /* stock route	a route taken by livestock herds */
    public const FEATURE_CODE_R_TNL = 'TNL'; /* tunnel	a subterranean passageway for transportation */
    public const FEATURE_CODE_R_TNLN = 'TNLN'; /* natural tunnel	a cave that is open at both ends */
    public const FEATURE_CODE_R_TNLRD = 'TNLRD'; /* road tunnel	a tunnel through which a road passes */
    public const FEATURE_CODE_R_TNLRR = 'TNLRR'; /* railroad tunnel	a tunnel through which a railroad passes */
    public const FEATURE_CODE_R_TNLS = 'TNLS'; /* tunnels	subterranean passageways for transportation */
    public const FEATURE_CODE_R_TRL = 'TRL'; /* trail	a path, track, or route used by pedestrians, animals, or off-road vehicles */

    /* */
    public const FEATURE_CODE_S_ADMF = 'ADMF'; /* administrative facility	a government building */
    public const FEATURE_CODE_S_AGRF = 'AGRF'; /* agricultural facility	a building and/or tract of land used for improving agriculture */
    public const FEATURE_CODE_S_AIRB = 'AIRB'; /* airbase	an area used to store supplies, provide barracks for air force personnel, hangars and runways for aircraft, and from which operations are initiated */
    public const FEATURE_CODE_S_AIRF = 'AIRF'; /* airfield	a place on land where aircraft land and take off; no facilities provided for the commercial handling of passengers and cargo */
    public const FEATURE_CODE_S_AIRH = 'AIRH'; /* heliport	a place where helicopters land and take off */
    public const FEATURE_CODE_S_AIRP = 'AIRP'; /* airport	a place where aircraft regularly land and take off, with runways, navigational aids, and major facilities for the commercial handling of passengers and cargo */
    public const FEATURE_CODE_S_AIRQ = 'AIRQ'; /* abandoned airfield */
    public const FEATURE_CODE_S_AIRT = 'AIRT'; /* terminal	airport facilities for the handling of freight and passengers */
    public const FEATURE_CODE_S_AMTH = 'AMTH'; /* amphitheater	an oval or circular structure with rising tiers of seats about a stage or open space */
    public const FEATURE_CODE_S_ANS = 'ANS'; /* archaeological/prehistoric site	a place where archeological remains, old structures, or cultural artifacts are located */
    public const FEATURE_CODE_S_AQC = 'AQC'; /* aquaculture facility	facility or area for the cultivation of aquatic animals and plants, especially fish, shellfish, and seaweed, in natural or controlled marine or freshwater environments; underwater agriculture */
    public const FEATURE_CODE_S_ARCH = 'ARCH'; /* arch	a natural or man-made structure in the form of an arch */
    public const FEATURE_CODE_S_ARCHV = 'ARCHV'; /* archive	a place or institution where documents are preserved */
    public const FEATURE_CODE_S_ART = 'ART'; /* piece of art	a piece of art, like a sculpture, painting. In contrast to monument (MNMT) it is not commemorative. */
    public const FEATURE_CODE_S_ASTR = 'ASTR'; /* astronomical station	a point on the earth whose position has been determined by observations of celestial bodies */
    public const FEATURE_CODE_S_ASYL = 'ASYL'; /* asylum	a facility where the insane are cared for and protected */
    public const FEATURE_CODE_S_ATHF = 'ATHF'; /* athletic field	a tract of land used for playing team sports, and athletic track and field events */
    public const FEATURE_CODE_S_ATM = 'ATM'; /* automatic teller machine	An unattended electronic machine in a public place, connected to a data system and related equipment and activated by a bank customer to obtain cash withdrawals and other banking services. */
    public const FEATURE_CODE_S_BANK = 'BANK'; /* bank	A business establishment in which money is kept for saving or commercial purposes or is invested, supplied for loans, or exchanged. */
    public const FEATURE_CODE_S_BCN = 'BCN'; /* beacon	a fixed artificial navigation mark */
    public const FEATURE_CODE_S_BDG = 'BDG'; /* bridge	a structure erected across an obstacle such as a stream, road, etc., in order to carry roads, railroads, and pedestrians across */
    public const FEATURE_CODE_S_BDGQ = 'BDGQ'; /* ruined bridge	a destroyed or decayed bridge which is no longer functional */
    public const FEATURE_CODE_S_BLDA = 'BLDA'; /* apartment building	a building containing several individual apartments */
    public const FEATURE_CODE_S_BLDG = 'BLDG'; /* building(s)	a structure built for permanent use, as a house, factory, etc. */
    public const FEATURE_CODE_S_BLDO = 'BLDO'; /* office building	commercial building where business and/or services are conducted */
    public const FEATURE_CODE_S_BP = 'BP'; /* boundary marker	a fixture marking a point along a boundary */
    public const FEATURE_CODE_S_BRKS = 'BRKS'; /* barracks	a building for lodging military personnel */
    public const FEATURE_CODE_S_BRKW = 'BRKW'; /* breakwater	a structure erected to break the force of waves at the entrance to a harbor or port */
    public const FEATURE_CODE_S_BSTN = 'BSTN'; /* baling station	a facility for baling agricultural products */
    public const FEATURE_CODE_S_BTYD = 'BTYD'; /* boatyard	a waterside facility for servicing, repairing, and building small vessels */
    public const FEATURE_CODE_S_BUR = 'BUR'; /* burial cave(s)	a cave used for human burials */
    public const FEATURE_CODE_S_BUSTN = 'BUSTN'; /* bus station	a facility comprising ticket office, platforms, etc. for loading and unloading passengers */
    public const FEATURE_CODE_S_BUSTP = 'BUSTP'; /* bus stop	a place lacking station facilities */
    public const FEATURE_CODE_S_CARN = 'CARN'; /* cairn	a heap of stones erected as a landmark or for other purposes */
    public const FEATURE_CODE_S_CAVE = 'CAVE'; /* cave(s)	an underground passageway or chamber, or cavity on the side of a cliff */
    public const FEATURE_CODE_S_CH = 'CH'; /* church	a building for public Christian worship */
    public const FEATURE_CODE_S_CMP = 'CMP';
    /* camp(s)	a site occupied by tents, huts, or other shelters for temporary use */public const FEATURE_CODE_S_CMPL = 'CMPL'; /* logging camp	a camp used by loggers */
    public const FEATURE_CODE_S_CMPLA = 'CMPLA'; /* labor camp	a camp used by migrant or temporary laborers */
    public const FEATURE_CODE_S_CMPMN = 'CMPMN'; /* mining camp	a camp used by miners */
    public const FEATURE_CODE_S_CMPO = 'CMPO'; /* oil camp	a camp used by oilfield workers */
    public const FEATURE_CODE_S_CMPQ = 'CMPQ'; /* abandoned camp	 */
    public const FEATURE_CODE_S_CMPRF = 'CMPRF'; /* refugee camp	a camp used by refugees */
    public const FEATURE_CODE_S_CMTY = 'CMTY'; /* cemetery	a burial place or ground */
    public const FEATURE_CODE_S_COMC = 'COMC'; /* communication center	a facility, including buildings, antennae, towers and electronic equipment for receiving and transmitting information */
    public const FEATURE_CODE_S_CRRL = 'CRRL'; /* corral(s)	a pen or enclosure for confining or capturing animals */
    public const FEATURE_CODE_S_CSNO = 'CSNO'; /* casino	a building used for entertainment, especially gambling */
    public const FEATURE_CODE_S_CSTL = 'CSTL'; /* castle	a large fortified building or set of buildings */
    public const FEATURE_CODE_S_CSTM = 'CSTM'; /* customs house	a building in a port where customs and duties are paid, and where vessels are entered and cleared */
    public const FEATURE_CODE_S_CTHSE = 'CTHSE'; /* courthouse	a building in which courts of law are held */
    public const FEATURE_CODE_S_CTRA = 'CTRA'; /* atomic center	a facility where atomic research is carried out */
    public const FEATURE_CODE_S_CTRCM = 'CTRCM'; /* community center	a facility for community recreation and other activities */
    public const FEATURE_CODE_S_CTRF = 'CTRF'; /* facility center	a place where more than one facility is situated */
    public const FEATURE_CODE_S_CTRM = 'CTRM'; /* medical center	a complex of health care buildings including two or more of the following: hospital, medical school, clinic, pharmacy, doctor's offices, etc. */
    public const FEATURE_CODE_S_CTRR = 'CTRR'; /* religious center	a facility where more than one religious activity is carried out, e.g., retreat, school, monastery, worship */
    public const FEATURE_CODE_S_CTRS = 'CTRS'; /* space center	a facility for launching, tracking, or controlling satellites and space vehicles */
    public const FEATURE_CODE_S_CVNT = 'CVNT'; /* convent	a building where a community of nuns lives in seclusion */
    public const FEATURE_CODE_S_DAM = 'DAM'; /* dam	a barrier constructed across a stream to impound water */
    public const FEATURE_CODE_S_DAMQ = 'DAMQ'; /* ruined dam	a destroyed or decayed dam which is no longer functional */
    public const FEATURE_CODE_S_DAMSB = 'DAMSB'; /* sub-surface dam	a dam put down to bedrock in a sand river */
    public const FEATURE_CODE_S_DARY = 'DARY'; /* dairy	a facility for the processing, sale and distribution of milk or milk products */
    public const FEATURE_CODE_S_DCKD = 'DCKD'; /* dry dock	a dock providing support for a vessel, and means for removing the water so that the bottom of the vessel can be exposed */
    public const FEATURE_CODE_S_DCKY = 'DCKY'; /* dockyard	a facility for servicing, building, or repairing ships */
    public const FEATURE_CODE_S_DIKE = 'DIKE'; /* dike	an earth or stone embankment usually constructed for flood or stream control */
    public const FEATURE_CODE_S_DIP = 'DIP'; /* diplomatic facility	office, residence, or facility of a foreign government, which may include an embassy, consulate, chancery, office of charge d'affaires, or other diplomatic, economic, military, or cultural mission */
    public const FEATURE_CODE_S_DPOF = 'DPOF'; /* fuel depot	an area where fuel is stored */
    public const FEATURE_CODE_S_EST = 'EST'; /* estate(s)	a large commercialized agricultural landholding with associated buildings and other facilities */
    public const FEATURE_CODE_S_ESTO = 'ESTO'; /* oil palm plantation	an estate specializing in the cultivation of oil palm trees */
    public const FEATURE_CODE_S_ESTR = 'ESTR'; /* rubber plantation	an estate which specializes in growing and tapping rubber trees */
    public const FEATURE_CODE_S_ESTSG = 'ESTSG'; /* sugar plantation	an estate that specializes in growing sugar cane */
    public const FEATURE_CODE_S_ESTT = 'ESTT'; /* tea plantation	an estate which specializes in growing tea bushes */
    public const FEATURE_CODE_S_ESTX = 'ESTX'; /* section of estate	 */
    public const FEATURE_CODE_S_FCL = 'FCL'; /* facility	a building or buildings housing a center, institute, foundation, hospital, prison, mission, courthouse, etc. */
    public const FEATURE_CODE_S_FNDY = 'FNDY'; /* foundry	a building or works where metal casting is carried out */
    public const FEATURE_CODE_S_FRM = 'FRM'; /* farm	a tract of land with associated buildings devoted to agriculture */
    public const FEATURE_CODE_S_FRMQ = 'FRMQ'; /* abandoned farm	 */
    public const FEATURE_CODE_S_FRMS = 'FRMS'; /* farms	tracts of land with associated buildings devoted to agriculture */
    public const FEATURE_CODE_S_FRMT = 'FRMT'; /* farmstead	the buildings and adjacent service areas of a farm */
    public const FEATURE_CODE_S_FT = 'FT'; /* fort	a defensive structure or earthworks */
    public const FEATURE_CODE_S_FY = 'FY'; /* ferry	a boat or other floating conveyance and terminal facilities regularly used to transport people and vehicles across a waterbody */
    public const FEATURE_CODE_S_FYT = 'FYT'; /* ferry terminal	a place where ferries pick-up and discharge passengers, vehicles and or cargo */
    public const FEATURE_CODE_S_GATE = 'GATE'; /* gate	a controlled access entrance or exit */
    public const FEATURE_CODE_S_GDN = 'GDN'; /* garden(s)	an enclosure for displaying selected plant or animal life */
    public const FEATURE_CODE_S_GHAT = 'GHAT'; /* ghat	a set of steps leading to a river, which are of religious significance, and at their base is usually a platform for bathing */
    public const FEATURE_CODE_S_GHSE = 'GHSE'; /* guest house	a house used to provide lodging for paying guests */
    public const FEATURE_CODE_S_GOSP = 'GOSP'; /* gas-oil separator plant	a facility for separating gas from oil */
    public const FEATURE_CODE_S_GOVL = 'GOVL'; /* local government office	a facility housing local governmental offices, usually a city, town, or village hall */
    public const FEATURE_CODE_S_GRVE = 'GRVE'; /* grave	a burial site */
    public const FEATURE_CODE_S_HERM = 'HERM'; /* hermitage	a secluded residence, usually for religious sects */
    public const FEATURE_CODE_S_HLT = 'HLT'; /* halting place	a place where caravans stop for rest */
    public const FEATURE_CODE_S_HMSD = 'HMSD'; /* homestead	a residence, owner's or manager's, on a sheep or cattle station, woolshed, outcamp, or Aboriginal outstation, specific to Australia and New Zealand */
    public const FEATURE_CODE_S_HSE = 'HSE'; /* house(s)	a building used as a human habitation */
    public const FEATURE_CODE_S_HSEC = 'HSEC'; /* country house	a large house, mansion, or chateau, on a large estate */
    public const FEATURE_CODE_S_HSP = 'HSP'; /* hospital	a building in which sick or injured, especially those confined to bed, are medically treated */
    public const FEATURE_CODE_S_HSPC = 'HSPC'; /* clinic	a medical facility associated with a hospital for outpatients */
    public const FEATURE_CODE_S_HSPD = 'HSPD'; /* dispensary	a building where medical or dental aid is dispensed */
    public const FEATURE_CODE_S_HSPL = 'HSPL'; /* leprosarium	an asylum or hospital for lepers */
    public const FEATURE_CODE_S_HSTS = 'HSTS'; /* historical site	a place of historical importance */
    public const FEATURE_CODE_S_HTL = 'HTL'; /* hotel	a building providing lodging and/or meals for the public */
    public const FEATURE_CODE_S_HUT = 'HUT'; /* hut	a small primitive house */
    public const FEATURE_CODE_S_HUTS = 'HUTS'; /* huts	small primitive houses */
    public const FEATURE_CODE_S_INSM = 'INSM'; /* military installation	a facility for use of and control by armed forces */
    public const FEATURE_CODE_S_ITTR = 'ITTR'; /* research institute	a facility where research is carried out */
    public const FEATURE_CODE_S_JTY = 'JTY'; /* jetty	a structure built out into the water at a river mouth or harbor entrance to regulate currents and silting */
    public const FEATURE_CODE_S_LDNG = 'LDNG'; /* landing	a place where boats receive or discharge passengers and freight, but lacking most port facilities */
    public const FEATURE_CODE_S_LEPC = 'LEPC'; /* leper colony	a settled area inhabited by lepers in relative isolation */
    public const FEATURE_CODE_S_LIBR = 'LIBR'; /* library	A place in which information resources such as books are kept for reading, reference, or lending. */
    public const FEATURE_CODE_S_LNDF = 'LNDF'; /* landfill	a place for trash and garbage disposal in which the waste is buried between layers of earth to build up low-lying land */
    public const FEATURE_CODE_S_LOCK = 'LOCK'; /* lock(s)	a basin in a waterway with gates at each end by means of which vessels are passed from one water level to another */
    public const FEATURE_CODE_S_LTHSE = 'LTHSE'; /* lighthouse	a distinctive structure exhibiting a major navigation light */
    public const FEATURE_CODE_S_MALL = 'MALL'; /* mall	A large, often enclosed shopping complex containing various stores, businesses, and restaurants usually accessible by common passageways. */
    public const FEATURE_CODE_S_MAR = 'MAR'; /* marina	a harbor facility for small boats, yachts, etc. */
    public const FEATURE_CODE_S_MFG = 'MFG'; /* factory	one or more buildings where goods are manufactured, processed or fabricated */
    public const FEATURE_CODE_S_MFGB = 'MFGB'; /* brewery	one or more buildings where beer is brewed */
    public const FEATURE_CODE_S_MFGC = 'MFGC'; /* cannery	a building where food items are canned */
    public const FEATURE_CODE_S_MFGCU = 'MFGCU'; /* copper works	a facility for processing copper ore */
    public const FEATURE_CODE_S_MFGLM = 'MFGLM'; /* limekiln	a furnace in which limestone is reduced to lime */
    public const FEATURE_CODE_S_MFGM = 'MFGM'; /* munitions plant	a factory where ammunition is made */
    public const FEATURE_CODE_S_MFGPH = 'MFGPH'; /* phosphate works	a facility for producing fertilizer */
    public const FEATURE_CODE_S_MFGQ = 'MFGQ'; /* abandoned factory	 */
    public const FEATURE_CODE_S_MFGSG = 'MFGSG'; /* sugar refinery	a facility for converting raw sugar into refined sugar */
    public const FEATURE_CODE_S_MKT = 'MKT'; /* market	a place where goods are bought and sold at regular intervals */
    public const FEATURE_CODE_S_ML = 'ML'; /* mill(s)	a building housing machines for transforming, shaping, finishing, grinding, or extracting products */
    public const FEATURE_CODE_S_MLM = 'MLM'; /* ore treatment plant	a facility for improving the metal content of ore by concentration */
    public const FEATURE_CODE_S_MLO = 'MLO'; /* olive oil mill	a mill where oil is extracted from olives */
    public const FEATURE_CODE_S_MLSG = 'MLSG'; /* sugar mill	a facility where sugar cane is processed into raw sugar */
    public const FEATURE_CODE_S_MLSGQ = 'MLSGQ'; /* former sugar mill	a sugar mill no longer used as a sugar mill */
    public const FEATURE_CODE_S_MLSW = 'MLSW'; /* sawmill	a mill where logs or lumber are sawn to specified shapes and sizes */
    public const FEATURE_CODE_S_MLWND = 'MLWND'; /* windmill	a mill or water pump powered by wind */
    public const FEATURE_CODE_S_MLWTR = 'MLWTR'; /* water mill	a mill powered by running water */
    public const FEATURE_CODE_S_MN = 'MN'; /* mine(s)	a site where mineral ores are extracted from the ground by excavating surface pits and subterranean passages */
    public const FEATURE_CODE_S_MNAU = 'MNAU'; /* gold mine(s)	a mine where gold ore, or alluvial gold is extracted */
    public const FEATURE_CODE_S_MNC = 'MNC'; /* coal mine(s)	a mine where coal is extracted */
    public const FEATURE_CODE_S_MNCR = 'MNCR'; /* chrome mine(s)	a mine where chrome ore is extracted */
    public const FEATURE_CODE_S_MNCU = 'MNCU'; /* copper mine(s)	a mine where copper ore is extracted */
    public const FEATURE_CODE_S_MNFE = 'MNFE'; /* iron mine(s)	a mine where iron ore is extracted */
    public const FEATURE_CODE_S_MNMT = 'MNMT'; /* monument	a commemorative structure or statue */
    public const FEATURE_CODE_S_MNN = 'MNN'; /* salt mine(s)	a mine from which salt is extracted */
    public const FEATURE_CODE_S_MNQ = 'MNQ'; /* abandoned mine	 */
    public const FEATURE_CODE_S_MNQR = 'MNQR'; /* quarry(-ies)	a surface mine where building stone or gravel and sand, etc. are extracted */
    public const FEATURE_CODE_S_MOLE = 'MOLE'; /* mole	a massive structure of masonry or large stones serving as a pier or breakwater */
    public const FEATURE_CODE_S_MSQE = 'MSQE'; /* mosque	a building for public Islamic worship */
    public const FEATURE_CODE_S_MSSN = 'MSSN'; /* mission	a place characterized by dwellings, school, church, hospital and other facilities operated by a religious group for the purpose of providing charitable services and to propagate religion */
    public const FEATURE_CODE_S_MSSNQ = 'MSSNQ'; /* abandoned mission	 */
    public const FEATURE_CODE_S_MSTY = 'MSTY'; /* monastery	a building and grounds where a community of monks lives in seclusion */
    public const FEATURE_CODE_S_MTRO = 'MTRO'; /* metro station	metro station (Underground, Tube, or Metro) */
    public const FEATURE_CODE_S_MUS = 'MUS'; /* museum	a building where objects of permanent interest in one or more of the arts and sciences are preserved and exhibited */
    public const FEATURE_CODE_S_NOV = 'NOV'; /* novitiate	a religious house or school where novices are trained */
    public const FEATURE_CODE_S_NSY = 'NSY'; /* nursery(-ies)	a place where plants are propagated for transplanting or grafting */
    public const FEATURE_CODE_S_OBPT = 'OBPT'; /* observation point	a wildlife or scenic observation point */
    public const FEATURE_CODE_S_OBS = 'OBS'; /* observatory	a facility equipped for observation of atmospheric or space phenomena */
    public const FEATURE_CODE_S_OBSR = 'OBSR'; /* radio observatory	a facility equipped with an array of antennae for receiving radio waves from space */
    public const FEATURE_CODE_S_OILJ = 'OILJ'; /* oil pipeline junction	a section of an oil pipeline where two or more pipes join together */
    public const FEATURE_CODE_S_OILQ = 'OILQ'; /* abandoned oil well	 */
    public const FEATURE_CODE_S_OILR = 'OILR'; /* oil refinery	a facility for converting crude oil into refined petroleum products */
    public const FEATURE_CODE_S_OILT = 'OILT'; /* tank farm	a tract of land occupied by large, cylindrical, metal tanks in which oil or liquid petrochemicals are stored */
    public const FEATURE_CODE_S_OILW = 'OILW'; /* oil well	a well from which oil may be pumped */
    public const FEATURE_CODE_S_OPRA = 'OPRA'; /* opera house	A theater designed chiefly for the performance of operas. */
    public const FEATURE_CODE_S_PAL = 'PAL'; /* palace	a large stately house, often a royal or presidential residence */
    public const FEATURE_CODE_S_PGDA = 'PGDA'; /* pagoda	a tower-like storied structure, usually a Buddhist shrine */
    public const FEATURE_CODE_S_PIER = 'PIER'; /* pier	a structure built out into navigable water on piles providing berthing for ships and recreation */
    public const FEATURE_CODE_S_PKLT = 'PKLT'; /* parking lot	an area used for parking vehicles */
    public const FEATURE_CODE_S_PMPO = 'PMPO'; /* oil pumping station	a facility for pumping oil through a pipeline */
    public const FEATURE_CODE_S_PMPW = 'PMPW'; /* water pumping station	a facility for pumping water from a major well or through a pipeline */
    public const FEATURE_CODE_S_PO = 'PO'; /* post office	a public building in which mail is received, sorted and distributed */
    public const FEATURE_CODE_S_PP = 'PP'; /* police post	a building in which police are stationed */
    public const FEATURE_CODE_S_PPQ = 'PPQ'; /* abandoned police post	 */
    public const FEATURE_CODE_S_PRKGT = 'PRKGT'; /* park gate	a controlled access to a park */
    public const FEATURE_CODE_S_PRKHQ = 'PRKHQ'; /* park headquarters	a park administrative facility */
    public const FEATURE_CODE_S_PRN = 'PRN'; /* prison	a facility for confining prisoners */
    public const FEATURE_CODE_S_PRNJ = 'PRNJ'; /* reformatory	a facility for confining, training, and reforming young law offenders */
    public const FEATURE_CODE_S_PRNQ = 'PRNQ'; /* abandoned prison	 */
    public const FEATURE_CODE_S_PS = 'PS'; /* power station	a facility for generating electric power */
    public const FEATURE_CODE_S_PSH = 'PSH'; /* hydroelectric power station	a building where electricity is generated from water power */
    public const FEATURE_CODE_S_PSN = 'PSN'; /* nuclear power station	nuclear power station */
    public const FEATURE_CODE_S_PSTB = 'PSTB'; /* border post	a post or station at an international boundary for the regulation of movement of people and goods */
    public const FEATURE_CODE_S_PSTC = 'PSTC'; /* customs post	a building at an international boundary where customs and duties are paid on goods */
    public const FEATURE_CODE_S_PSTP = 'PSTP'; /* patrol post	a post from which patrols are sent out */
    public const FEATURE_CODE_S_PYR = 'PYR'; /* pyramid	an ancient massive structure of square ground plan with four triangular faces meeting at a point and used for enclosing tombs */
    public const FEATURE_CODE_S_PYRS = 'PYRS'; /* pyramids	ancient massive structures of square ground plan with four triangular faces meeting at a point and used for enclosing tombs */
    public const FEATURE_CODE_S_QUAY = 'QUAY'; /* quay	a structure of solid construction along a shore or bank which provides berthing for ships and which generally provides cargo handling facilities */
    public const FEATURE_CODE_S_RDCR = 'RDCR'; /* traffic circle	a road junction formed around a central circle about which traffic moves in one direction only */
    public const FEATURE_CODE_S_RDIN = 'RDIN'; /* intersection	a junction of two or more highways by a system of separate levels that permit traffic to pass from one to another without the crossing of traffic streams */
    public const FEATURE_CODE_S_RECG = 'RECG'; /* golf course	a recreation field where golf is played */
    public const FEATURE_CODE_S_RECR = 'RECR'; /* racetrack	a track where races are held */
    public const FEATURE_CODE_S_REST = 'REST'; /* restaurant	A place where meals are served to the public */
    public const FEATURE_CODE_S_RET = 'RET'; /* store	a building where goods and/or services are offered for sale */
    public const FEATURE_CODE_S_RHSE = 'RHSE'; /* resthouse	a structure maintained for the rest and shelter of travelers */
    public const FEATURE_CODE_S_RKRY = 'RKRY'; /* rookery	a breeding place of a colony of birds or seals */
    public const FEATURE_CODE_S_RLG = 'RLG'; /* religious site	an ancient site of significant religious importance */
    public const FEATURE_CODE_S_RLGR = 'RLGR'; /* retreat	a place of temporary seclusion, especially for religious groups */
    public const FEATURE_CODE_S_RNCH = 'RNCH'; /* ranch(es)	a large farm specializing in extensive grazing of livestock */
    public const FEATURE_CODE_S_RSD = 'RSD'; /* railroad siding	a short track parallel to and joining the main track */
    public const FEATURE_CODE_S_RSGNL = 'RSGNL'; /* railroad signal	a signal at the entrance of a particular section of track governing the movement of trains */
    public const FEATURE_CODE_S_RSRT = 'RSRT'; /* resort	a specialized facility for vacation, health, or participation sports activities */
    public const FEATURE_CODE_S_RSTN = 'RSTN'; /* railroad station	a facility comprising ticket office, platforms, etc. for loading and unloading train passengers and freight */
    public const FEATURE_CODE_S_RSTNQ = 'RSTNQ'; /* abandoned railroad station	 */
    public const FEATURE_CODE_S_RSTP = 'RSTP'; /* railroad stop	a place lacking station facilities where trains stop to pick up and unload passengers and freight */
    public const FEATURE_CODE_S_RSTPQ = 'RSTPQ'; /* abandoned railroad stop	 */
    public const FEATURE_CODE_S_RUIN = 'RUIN'; /* ruin(s)	a destroyed or decayed structure which is no longer functional */
    public const FEATURE_CODE_S_SCH = 'SCH'; /* school	building(s) where instruction in one or more branches of knowledge takes place */
    public const FEATURE_CODE_S_SCHA = 'SCHA'; /* agricultural school	a school with a curriculum focused on agriculture */
    public const FEATURE_CODE_S_SCHC = 'SCHC'; /* college	the grounds and buildings of an institution of higher learning */
    public const FEATURE_CODE_S_SCHL = 'SCHL'; /* language school	Language Schools & Institutions */
    public const FEATURE_CODE_S_SCHM = 'SCHM'; /* military school	a school at which military science forms the core of the curriculum */
    public const FEATURE_CODE_S_SCHN = 'SCHN'; /* maritime school	a school at which maritime sciences form the core of the curriculum */
    public const FEATURE_CODE_S_SCHT = 'SCHT'; /* technical school	post-secondary school with a specifically technical or vocational curriculum */
    public const FEATURE_CODE_S_SECP = 'SECP'; /* State Exam Prep Centre	state exam preparation centres */
    public const FEATURE_CODE_S_SHPF = 'SHPF'; /* sheepfold	a fence or wall enclosure for sheep and other small herd animals */
    public const FEATURE_CODE_S_SHRN = 'SHRN'; /* shrine	a structure or place memorializing a person or religious concept */
    public const FEATURE_CODE_S_SHSE = 'SHSE'; /* storehouse	a building for storing goods, especially provisions */
    public const FEATURE_CODE_S_SLCE = 'SLCE'; /* sluice	a conduit or passage for carrying off surplus water from a waterbody, usually regulated by means of a sluice gate */
    public const FEATURE_CODE_S_SNTR = 'SNTR'; /* sanatorium	a facility where victims of physical or mental disorders are treated */
    public const FEATURE_CODE_S_SPA = 'SPA'; /* spa	a resort area usually developed around a medicinal spring */
    public const FEATURE_CODE_S_SPLY = 'SPLY'; /* spillway	a passage or outlet through which surplus water flows over, around or through a dam */
    public const FEATURE_CODE_S_SQR = 'SQR'; /* square	a broad, open, public area near the center of a town or city */
    public const FEATURE_CODE_S_STBL = 'STBL'; /* stable	a building for the shelter and feeding of farm animals, especially horses */
    public const FEATURE_CODE_S_STDM = 'STDM'; /* stadium	a structure with an enclosure for athletic games with tiers of seats for spectators */
    public const FEATURE_CODE_S_STNB = 'STNB'; /* scientific research base	a scientific facility used as a base from which research is carried out or monitored */
    public const FEATURE_CODE_S_STNC = 'STNC'; /* coast guard station	a facility from which the coast is guarded by armed vessels */
    public const FEATURE_CODE_S_STNE = 'STNE'; /* experiment station	a facility for carrying out experiments */
    public const FEATURE_CODE_S_STNF = 'STNF'; /* forest station	a collection of buildings and facilities for carrying out forest management */
    public const FEATURE_CODE_S_STNI = 'STNI'; /* inspection station	a station at which vehicles, goods, and people are inspected */
    public const FEATURE_CODE_S_STNM = 'STNM'; /* meteorological station	a station at which weather elements are recorded */
    public const FEATURE_CODE_S_STNR = 'STNR'; /* radio station	a facility for producing and transmitting information by radio waves */
    public const FEATURE_CODE_S_STNS = 'STNS'; /* satellite station	a facility for tracking and communicating with orbiting satellites */
    public const FEATURE_CODE_S_STNW = 'STNW'; /* whaling station	a facility for butchering whales and processing train oil */
    public const FEATURE_CODE_S_STPS = 'STPS'; /* steps	stones or slabs placed for ease in ascending or descending a steep slope */
    public const FEATURE_CODE_S_SWT = 'SWT'; /* sewage treatment plant	facility for the processing of sewage and/or wastewater */
    public const FEATURE_CODE_S_SYG = 'SYG'; /* synagogue	a place for Jewish worship and religious instruction */
    public const FEATURE_CODE_S_THTR = 'THTR'; /* theater	A building, room, or outdoor structure for the presentation of plays, films, or other dramatic performances */
    public const FEATURE_CODE_S_TMB = 'TMB'; /* tomb(s)	a structure for interring bodies */
    public const FEATURE_CODE_S_TMPL = 'TMPL'; /* temple(s)	an edifice dedicated to religious worship */
    public const FEATURE_CODE_S_TNKD = 'TNKD'; /* cattle dipping tank	a small artificial pond used for immersing cattle in chemically treated water for disease control */
    public const FEATURE_CODE_S_TOLL = 'TOLL'; /* toll gate/barrier	highway toll collection station */
    public const FEATURE_CODE_S_TOWR = 'TOWR'; /* tower	a high conspicuous structure, typically much higher than its diameter */
    public const FEATURE_CODE_S_TRAM = 'TRAM'; /* tram	rail vehicle along urban streets (also known as streetcar or trolley) */
    public const FEATURE_CODE_S_TRANT = 'TRANT'; /* transit terminal	facilities for the handling of vehicular freight and passengers */
    public const FEATURE_CODE_S_TRIG = 'TRIG'; /* triangulation station	a point on the earth whose position has been determined by triangulation */
    public const FEATURE_CODE_S_TRMO = 'TRMO'; /* oil pipeline terminal	a tank farm or loading facility at the end of an oil pipeline */
    public const FEATURE_CODE_S_TWO = 'TWO'; /* temp work office	Temporary Work Offices */
    public const FEATURE_CODE_S_UNIP = 'UNIP'; /* university prep school	University Preparation Schools & Institutions */
    public const FEATURE_CODE_S_UNIV = 'UNIV'; /* university	An institution for higher learning with teaching and research facilities constituting a graduate school and professional schools that award master's degrees and doctorates and an undergraduate division that awards bachelor's degrees. */
    public const FEATURE_CODE_S_USGE = 'USGE'; /* united states government establishment	a facility operated by the United States Government in Panama */
    public const FEATURE_CODE_S_VETF = 'VETF'; /* veterinary facility	a building or camp at which veterinary services are available */
    public const FEATURE_CODE_S_WALL = 'WALL'; /* wall	a thick masonry structure, usually enclosing a field or building, or forming the side of a structure */
    public const FEATURE_CODE_S_WALLA = 'WALLA'; /* ancient wall	the remains of a linear defensive stone structure */
    public const FEATURE_CODE_S_WEIR = 'WEIR'; /* weir(s)	a small dam in a stream, designed to raise the water level or to divert stream flow through a desired channel */
    public const FEATURE_CODE_S_WHRF = 'WHRF'; /* wharf(-ves)	a structure of open rather than solid construction along a shore or a bank which provides berthing for ships and cargo-handling facilities */
    public const FEATURE_CODE_S_WRCK = 'WRCK'; /* wreck	the site of the remains of a wrecked vessel */
    public const FEATURE_CODE_S_WTRW = 'WTRW'; /* waterworks	a facility for supplying potable water through a water source and a system of pumps and filtration beds */
    public const FEATURE_CODE_S_ZNF = 'ZNF'; /* free trade zone	an area, usually a section of a port, where goods may be received and shipped free of customs duty and of most customs regulations */
    public const FEATURE_CODE_S_ZOO = 'ZOO'; /* zoo	a zoological garden or park where wild animals are kept for exhibition */

    /* */
    public const FEATURE_CODE_T_ASPH = 'ASPH'; /* asphalt lake	a small basin containing naturally occurring asphalt */
    public const FEATURE_CODE_T_ATOL = 'ATOL'; /* atoll(s)	a ring-shaped coral reef which has closely spaced islands on it encircling a lagoon */
    public const FEATURE_CODE_T_BAR = 'BAR'; /* bar	a shallow ridge or mound of coarse unconsolidated material in a stream channel, at the mouth of a stream, estuary, or lagoon and in the wave-break zone along coasts */
    public const FEATURE_CODE_T_BCH = 'BCH'; /* beach	a shore zone of coarse unconsolidated sediment that extends from the low-water line to the highest reach of storm waves */
    public const FEATURE_CODE_T_BCHS = 'BCHS'; /* beaches	a shore zone of coarse unconsolidated sediment that extends from the low-water line to the highest reach of storm waves */
    public const FEATURE_CODE_T_BDLD = 'BDLD'; /* badlands	an area characterized by a maze of very closely spaced, deep, narrow, steep-sided ravines, and sharp crests and pinnacles */
    public const FEATURE_CODE_T_BLDR = 'BLDR'; /* boulder field	a high altitude or high latitude bare, flat area covered with large angular rocks */
    public const FEATURE_CODE_T_BLHL = 'BLHL'; /* blowhole(s)	a hole in coastal rock through which sea water is forced by a rising tide or waves and spurted through an outlet into the air */
    public const FEATURE_CODE_T_BLOW = 'BLOW'; /* blowout(s)	a small depression in sandy terrain, caused by wind erosion */
    public const FEATURE_CODE_T_BNCH = 'BNCH'; /* bench	a long, narrow bedrock platform bounded by steeper slopes above and below, usually overlooking a waterbody */
    public const FEATURE_CODE_T_BUTE = 'BUTE'; /* butte(s)	a small, isolated, usually flat-topped hill with steep sides */
    public const FEATURE_CODE_T_CAPE = 'CAPE'; /* cape	a land area, more prominent than a point, projecting into the sea and marking a notable change in coastal direction */
    public const FEATURE_CODE_T_CFT = 'CFT'; /* cleft(s)	a deep narrow slot, notch, or groove in a coastal cliff */
    public const FEATURE_CODE_T_CLDA = 'CLDA'; /* caldera	a depression measuring kilometers across formed by the collapse of a volcanic mountain */
    public const FEATURE_CODE_T_CLF = 'CLF'; /* cliff(s)	a high, steep to perpendicular slope overlooking a waterbody or lower area */
    public const FEATURE_CODE_T_CNYN = 'CNYN'; /* canyon	a deep, narrow valley with steep sides cutting into a plateau or mountainous area */
    public const FEATURE_CODE_T_CONE = 'CONE'; /* cone(s)	a conical landform composed of mud or volcanic material */
    public const FEATURE_CODE_T_CRDR = 'CRDR'; /* corridor	a strip or area of land having significance as an access way */
    public const FEATURE_CODE_T_CRQ = 'CRQ'; /* cirque	a bowl-like hollow partially surrounded by cliffs or steep slopes at the head of a glaciated valley */
    public const FEATURE_CODE_T_CRQS = 'CRQS'; /* cirques	bowl-like hollows partially surrounded by cliffs or steep slopes at the head of a glaciated valley */
    public const FEATURE_CODE_T_CRTR = 'CRTR'; /* crater(s)	a generally circular saucer or bowl-shaped depression caused by volcanic or meteorite explosive action */
    public const FEATURE_CODE_T_CUET = 'CUET'; /* cuesta(s)	an asymmetric ridge formed on tilted strata */
    public const FEATURE_CODE_T_DLTA = 'DLTA'; /* delta	a flat plain formed by alluvial deposits at the mouth of a stream */
    public const FEATURE_CODE_T_DPR = 'DPR'; /* depression(s)	a low area surrounded by higher land and usually characterized by interior drainage */
    public const FEATURE_CODE_T_DSRT = 'DSRT'; /* desert	a large area with little or no vegetation due to extreme environmental conditions */
    public const FEATURE_CODE_T_DUNE = 'DUNE'; /* dune(s)	a wave form, ridge or star shape feature composed of sand */
    public const FEATURE_CODE_T_DVD = 'DVD'; /* divide	a line separating adjacent drainage basins */
    public const FEATURE_CODE_T_ERG = 'ERG'; /* sandy desert	an extensive tract of shifting sand and sand dunes */
    public const FEATURE_CODE_T_FAN = 'FAN'; /* fan(s)	a fan-shaped wedge of coarse alluvium with apex merging with a mountain stream bed and the fan spreading out at a low angle slope onto an adjacent plain */
    public const FEATURE_CODE_T_FORD = 'FORD'; /* ford	a shallow part of a stream which can be crossed on foot or by land vehicle */
    public const FEATURE_CODE_T_FSR = 'FSR'; /* fissure	a crack associated with volcanism */
    public const FEATURE_CODE_T_GAP = 'GAP'; /* gap	a low place in a ridge, not used for transportation */
    public const FEATURE_CODE_T_GRGE = 'GRGE'; /* gorge(s)	a short, narrow, steep-sided section of a stream valley */
    public const FEATURE_CODE_T_HDLD = 'HDLD'; /* headland	a high projection of land extending into a large body of water beyond the line of the coast */
    public const FEATURE_CODE_T_HLL = 'HLL'; /* hill	a rounded elevation of limited extent rising above the surrounding land with local relief of less than 300m */
    public const FEATURE_CODE_T_HLLS = 'HLLS'; /* hills	rounded elevations of limited extent rising above the surrounding land with local relief of less than 300m */
    public const FEATURE_CODE_T_HMCK = 'HMCK'; /* hammock(s)	a patch of ground, distinct from and slightly above the surrounding plain or wetland. Often occurs in groups */
    public const FEATURE_CODE_T_HMDA = 'HMDA'; /* rock desert	a relatively sand-free, high bedrock plateau in a hot desert, with or without a gravel veneer */
    public const FEATURE_CODE_T_INTF = 'INTF'; /* interfluve	a relatively undissected upland between adjacent stream valleys */
    public const FEATURE_CODE_T_ISL = 'ISL'; /* island	a tract of land, smaller than a continent, surrounded by water at high water */
    public const FEATURE_CODE_T_ISLET = 'ISLET'; /* islet	small island, bigger than rock, smaller than island. */
    public const FEATURE_CODE_T_ISLF = 'ISLF'; /* artificial island	an island created by landfill or diking and filling in a wetland, bay, or lagoon */
    public const FEATURE_CODE_T_ISLM = 'ISLM'; /* mangrove island	a mangrove swamp surrounded by a waterbody */
    public const FEATURE_CODE_T_ISLS = 'ISLS'; /* islands	tracts of land, smaller than a continent, surrounded by water at high water */
    public const FEATURE_CODE_T_ISLT = 'ISLT'; /* land-tied island	a coastal island connected to the mainland by barrier beaches, levees or dikes */
    public const FEATURE_CODE_T_ISLX = 'ISLX'; /* section of island */
    public const FEATURE_CODE_T_ISTH = 'ISTH'; /* isthmus	a narrow strip of land connecting two larger land masses and bordered by water */
    public const FEATURE_CODE_T_KRST = 'KRST'; /* karst area	a distinctive landscape developed on soluble rock such as limestone characterized by sinkholes, caves, disappearing streams, and underground drainage */
    public const FEATURE_CODE_T_LAVA = 'LAVA'; /* lava area	an area of solidified lava */
    public const FEATURE_CODE_T_LEV = 'LEV'; /* levee	a natural low embankment bordering a distributary or meandering stream; often built up artificially to control floods */
    public const FEATURE_CODE_T_MESA = 'MESA'; /* mesa(s)	a flat-topped, isolated elevation with steep slopes on all sides, less extensive than a plateau */
    public const FEATURE_CODE_T_MND = 'MND'; /* mound(s)	a low, isolated, rounded hill */
    public const FEATURE_CODE_T_MRN = 'MRN'; /* moraine	a mound, ridge, or other accumulation of glacial till */
    public const FEATURE_CODE_T_MT = 'MT'; /* mountain	an elevation standing high above the surrounding area with small summit area, steep slopes and local relief of 300m or more */
    public const FEATURE_CODE_T_MTS = 'MTS'; /* mountains	a mountain range or a group of mountains or high ridges */
    public const FEATURE_CODE_T_NKM = 'NKM'; /* meander neck	a narrow strip of land between the two limbs of a meander loop at its narrowest point */
    public const FEATURE_CODE_T_NTK = 'NTK'; /* nunatak	a rock or mountain peak protruding through glacial ice */
    public const FEATURE_CODE_T_NTKS = 'NTKS'; /* nunataks	rocks or mountain peaks protruding through glacial ice */
    public const FEATURE_CODE_T_PAN = 'PAN'; /* pan	a near-level shallow, natural depression or basin, usually containing an intermittent lake, pond, or pool */
    public const FEATURE_CODE_T_PANS = 'PANS'; /* pans	a near-level shallow, natural depression or basin, usually containing an intermittent lake, pond, or pool */
    public const FEATURE_CODE_T_PASS = 'PASS'; /* pass	a break in a mountain range or other high obstruction, used for transportation from one side to the other [See also gap] */
    public const FEATURE_CODE_T_PEN = 'PEN'; /* peninsula	an elongate area of land projecting into a body of water and nearly surrounded by water */
    public const FEATURE_CODE_T_PENX = 'PENX'; /* section of peninsula */
    public const FEATURE_CODE_T_PK = 'PK'; /* peak	a pointed elevation atop a mountain, ridge, or other hypsographic feature */
    public const FEATURE_CODE_T_PKS = 'PKS'; /* peaks	pointed elevations atop a mountain, ridge, or other hypsographic features */
    public const FEATURE_CODE_T_PLAT = 'PLAT'; /* plateau	an elevated plain with steep slopes on one or more sides, and often with incised streams */
    public const FEATURE_CODE_T_PLATX = 'PLATX'; /* section of plateau */
    public const FEATURE_CODE_T_PLDR = 'PLDR'; /* polder	an area reclaimed from the sea by diking and draining */
    public const FEATURE_CODE_T_PLN = 'PLN'; /* plain(s)	an extensive area of comparatively level to gently undulating land, lacking surface irregularities, and usually adjacent to a higher area */
    public const FEATURE_CODE_T_PLNX = 'PLNX'; /* section of plain */
    public const FEATURE_CODE_T_PROM = 'PROM'; /* promontory(-ies)	a bluff or prominent hill overlooking or projecting into a lowland */
    public const FEATURE_CODE_T_PT = 'PT'; /* point	a tapering piece of land projecting into a body of water, less prominent than a cape */
    public const FEATURE_CODE_T_PTS = 'PTS'; /* points	tapering pieces of land projecting into a body of water, less prominent than a cape */
    public const FEATURE_CODE_T_RDGB = 'RDGB'; /* beach ridge	a ridge of sand just inland and parallel to the beach, usually in series */
    public const FEATURE_CODE_T_RDGE = 'RDGE'; /* ridge(s)	a long narrow elevation with steep sides, and a more or less continuous crest */
    public const FEATURE_CODE_T_REG = 'REG'; /* stony desert	a desert plain characterized by a surface veneer of gravel and stones */
    public const FEATURE_CODE_T_RK = 'RK'; /* rock	a conspicuous, isolated rocky mass */
    public const FEATURE_CODE_T_RKFL = 'RKFL'; /* rockfall	an irregular mass of fallen rock at the base of a cliff or steep slope */
    public const FEATURE_CODE_T_RKS = 'RKS'; /* rocks	conspicuous, isolated rocky masses */
    public const FEATURE_CODE_T_SAND = 'SAND'; /* sand area	a tract of land covered with sand */
    public const FEATURE_CODE_T_SBED = 'SBED'; /* dry stream bed	a channel formerly containing the water of a stream */
    public const FEATURE_CODE_T_SCRP = 'SCRP'; /* escarpment	a long line of cliffs or steep slopes separating level surfaces above and below */
    public const FEATURE_CODE_T_SDL = 'SDL'; /* saddle	a broad, open pass crossing a ridge or between hills or mountains */
    public const FEATURE_CODE_T_SHOR = 'SHOR'; /* shore	a narrow zone bordering a waterbody which covers and uncovers at high and low water, respectively */
    public const FEATURE_CODE_T_SINK = 'SINK'; /* sinkhole	a small crater-shape depression in a karst area */
    public const FEATURE_CODE_T_SLID = 'SLID'; /* slide	a mound of earth material, at the base of a slope and the associated scoured area */
    public const FEATURE_CODE_T_SLP = 'SLP'; /* slope(s)	a surface with a relatively uniform slope angle */
    public const FEATURE_CODE_T_SPIT = 'SPIT'; /* spit	a narrow, straight or curved continuation of a beach into a waterbody */
    public const FEATURE_CODE_T_SPUR = 'SPUR'; /* spur(s)	a subordinate ridge projecting outward from a hill, mountain or other elevation */
    public const FEATURE_CODE_T_TAL = 'TAL'; /* talus slope	a steep concave slope formed by an accumulation of loose rock fragments at the base of a cliff or steep slope */
    public const FEATURE_CODE_T_TRGD = 'TRGD'; /* interdune trough(s)	a long wind-swept trough between parallel longitudinal dunes */
    public const FEATURE_CODE_T_TRR = 'TRR'; /* terrace	a long, narrow alluvial platform bounded by steeper slopes above and below, usually overlooking a waterbody */
    public const FEATURE_CODE_T_UPLD = 'UPLD'; /* upland	an extensive interior region of high land with low to moderate surface relief */
    public const FEATURE_CODE_T_VAL = 'VAL'; /* valley	an elongated depression usually traversed by a stream */
    public const FEATURE_CODE_T_VALG = 'VALG'; /* hanging valley	a valley the floor of which is notably higher than the valley or shore to which it leads; most common in areas that have been glaciated */
    public const FEATURE_CODE_T_VALS = 'VALS'; /* valleys	elongated depressions usually traversed by a stream */
    public const FEATURE_CODE_T_VALX = 'VALX'; /* section of valley */
    public const FEATURE_CODE_T_VLC = 'VLC'; /* volcano	a conical elevation composed of volcanic materials with a crater at the top */

    /* */
    public const FEATURE_CODE_U_APNU = 'APNU'; /* apron	a gentle slope, with a generally smooth surface, particularly found around groups of islands and seamounts */
    public const FEATURE_CODE_U_ARCU = 'ARCU'; /* arch	a low bulge around the southeastern end of the island of Hawaii */
    public const FEATURE_CODE_U_ARRU = 'ARRU'; /* arrugado	an area of subdued corrugations off Baja California */
    public const FEATURE_CODE_U_BDLU = 'BDLU'; /* borderland	a region adjacent to a continent, normally occupied by or bordering a shelf, that is highly irregular with depths well in excess of those typical of a shelf */
    public const FEATURE_CODE_U_BKSU = 'BKSU'; /* banks	elevations, typically located on a shelf, over which the depth of water is relatively shallow but sufficient for safe surface navigation */
    public const FEATURE_CODE_U_BNKU = 'BNKU'; /* bank	an elevation, typically located on a shelf, over which the depth of water is relatively shallow but sufficient for safe surface navigation */
    public const FEATURE_CODE_U_BSNU = 'BSNU'; /* basin	a depression more or less equidimensional in plan and of variable extent */
    public const FEATURE_CODE_U_CDAU = 'CDAU'; /* cordillera	an entire mountain system including the subordinate ranges, interior plateaus, and basins */
    public const FEATURE_CODE_U_CNSU = 'CNSU'; /* canyons	relatively narrow, deep depressions with steep sides, the bottom of which generally has a continuous slope */
    public const FEATURE_CODE_U_CNYU = 'CNYU'; /* canyon	a relatively narrow, deep depression with steep sides, the bottom of which generally has a continuous slope */
    public const FEATURE_CODE_U_CRSU = 'CRSU'; /* continental rise	a gentle slope rising from oceanic depths towards the foot of a continental slope */
    public const FEATURE_CODE_U_DEPU = 'DEPU'; /* deep	a localized deep area within the confines of a larger feature, such as a trough, basin or trench */
    public const FEATURE_CODE_U_EDGU = 'EDGU'; /* shelf edge	a line along which there is a marked increase of slope at the outer margin of a continental shelf or island shelf */
    public const FEATURE_CODE_U_ESCU = 'ESCU'; /* escarpment (or scarp)	an elongated and comparatively steep slope separating flat or gently sloping areas */
    public const FEATURE_CODE_U_FANU = 'FANU'; /* fan	a relatively smooth feature normally sloping away from the lower termination of a canyon or canyon system */
    public const FEATURE_CODE_U_FLTU = 'FLTU'; /* flat	a small level or nearly level area */
    public const FEATURE_CODE_U_FRZU = 'FRZU'; /* fracture zone	an extensive linear zone of irregular topography of the sea floor, characterized by steep-sided or asymmetrical ridges, troughs, or escarpments */
    public const FEATURE_CODE_U_FURU = 'FURU'; /* furrow	a closed, linear, narrow, shallow depression */
    public const FEATURE_CODE_U_GAPU = 'GAPU'; /* gap	a narrow break in a ridge or rise */
    public const FEATURE_CODE_U_GLYU = 'GLYU'; /* gully	a small valley-like feature */
    public const FEATURE_CODE_U_HLLU = 'HLLU'; /* hill	an elevation rising generally less than 500 meters */
    public const FEATURE_CODE_U_HLSU = 'HLSU'; /* hills	elevations rising generally less than 500 meters */
    public const FEATURE_CODE_U_HOLU = 'HOLU'; /* hole	a small depression of the sea floor */
    public const FEATURE_CODE_U_KNLU = 'KNLU'; /* knoll	an elevation rising generally more than 500 meters and less than 1,000 meters and of limited extent across the summit */
    public const FEATURE_CODE_U_KNSU = 'KNSU'; /* knolls	elevations rising generally more than 500 meters and less than 1,000 meters and of limited extent across the summits */
    public const FEATURE_CODE_U_LDGU = 'LDGU'; /* ledge	a rocky projection or outcrop, commonly linear and near shore */
    public const FEATURE_CODE_U_LEVU = 'LEVU'; /* levee	an embankment bordering a canyon, valley, or seachannel */
    public const FEATURE_CODE_U_MESU = 'MESU'; /* mesa	an isolated, extensive, flat-topped elevation on the shelf, with relatively steep sides */
    public const FEATURE_CODE_U_MNDU = 'MNDU'; /* mound	a low, isolated, rounded hill */
    public const FEATURE_CODE_U_MOTU = 'MOTU'; /* moat	an annular depression that may not be continuous, located at the base of many seamounts, islands, and other isolated elevations */
    public const FEATURE_CODE_U_MTU = 'MTU'; /* mountain	a well-delineated subdivision of a large and complex positive feature */
    public const FEATURE_CODE_U_PKSU = 'PKSU'; /* peaks	prominent elevations, part of a larger feature, either pointed or of very limited extent across the summit */
    public const FEATURE_CODE_U_PKU = 'PKU'; /* peak	a prominent elevation, part of a larger feature, either pointed or of very limited extent across the summit */
    public const FEATURE_CODE_U_PLNU = 'PLNU'; /* plain	a flat, gently sloping or nearly level region */
    public const FEATURE_CODE_U_PLTU = 'PLTU'; /* plateau	a comparatively flat-topped feature of considerable extent, dropping off abruptly on one or more sides */
    public const FEATURE_CODE_U_PNLU = 'PNLU'; /* pinnacle	a high tower or spire-shaped pillar of rock or coral, alone or cresting a summit */
    public const FEATURE_CODE_U_PRVU = 'PRVU'; /* province	a region identifiable by a group of similar physiographic features whose characteristics are markedly in contrast with surrounding areas */
    public const FEATURE_CODE_U_RDGU = 'RDGU'; /* ridge	a long narrow elevation with steep sides */
    public const FEATURE_CODE_U_RDSU = 'RDSU'; /* ridges	long narrow elevations with steep sides */
    public const FEATURE_CODE_U_RFSU = 'RFSU'; /* reefs	surface-navigation hazards composed of consolidated material */
    public const FEATURE_CODE_U_RFU = 'RFU'; /* reef	a surface-navigation hazard composed of consolidated material */
    public const FEATURE_CODE_U_RISU = 'RISU'; /* rise	a broad elevation that rises gently, and generally smoothly, from the sea floor */
    public const FEATURE_CODE_U_SCNU = 'SCNU'; /* seachannel	a continuously sloping, elongated depression commonly found in fans or plains and customarily bordered by levees on one or two sides */
    public const FEATURE_CODE_U_SCSU = 'SCSU'; /* seachannels	continuously sloping, elongated depressions commonly found in fans or plains and customarily bordered by levees on one or two sides */
    public const FEATURE_CODE_U_SDLU = 'SDLU'; /* saddle	a low part, resembling in shape a saddle, in a ridge or between contiguous seamounts */
    public const FEATURE_CODE_U_SHFU = 'SHFU'; /* shelf	a zone adjacent to a continent (or around an island) that extends from the low water line to a depth at which there is usually a marked increase of slope towards oceanic depths */
    public const FEATURE_CODE_U_SHLU = 'SHLU'; /* shoal	a surface-navigation hazard composed of unconsolidated material */
    public const FEATURE_CODE_U_SHSU = 'SHSU'; /* shoals	hazards to surface navigation composed of unconsolidated material */
    public const FEATURE_CODE_U_SHVU = 'SHVU'; /* shelf valley	a valley on the shelf, generally the shoreward extension of a canyon */
    public const FEATURE_CODE_U_SILU = 'SILU'; /* sill	the low part of a gap or saddle separating basins */
    public const FEATURE_CODE_U_SLPU = 'SLPU'; /* slope	the slope seaward from the shelf edge to the beginning of a continental rise or the point where there is a general reduction in slope */
    public const FEATURE_CODE_U_SMSU = 'SMSU'; /* seamounts	elevations rising generally more than 1,000 meters and of limited extent across the summit */
    public const FEATURE_CODE_U_SMU = 'SMU'; /* seamount	an elevation rising generally more than 1,000 meters and of limited extent across the summit */
    public const FEATURE_CODE_U_SPRU = 'SPRU'; /* spur	a subordinate elevation, ridge, or rise projecting outward from a larger feature */
    public const FEATURE_CODE_U_TERU = 'TERU'; /* terrace	a relatively flat horizontal or gently inclined surface, sometimes long and narrow, which is bounded by a steeper ascending slope on one side and by a steep descending slope on the opposite side */
    public const FEATURE_CODE_U_TMSU = 'TMSU'; /* tablemounts (or guyots)	seamounts having a comparatively smooth, flat top */
    public const FEATURE_CODE_U_TMTU = 'TMTU'; /* tablemount (or guyot)	a seamount having a comparatively smooth, flat top */
    public const FEATURE_CODE_U_TNGU = 'TNGU'; /* tongue	an elongate (tongue-like) extension of a flat sea floor into an adjacent higher feature */
    public const FEATURE_CODE_U_TRGU = 'TRGU'; /* trough	a long depression of the sea floor characteristically flat bottomed and steep sided, and normally shallower than a trench */
    public const FEATURE_CODE_U_TRNU = 'TRNU'; /* trench	a long, narrow, characteristically very deep and asymmetrical depression of the sea floor, with relatively steep sides */
    public const FEATURE_CODE_U_VALU = 'VALU'; /* valley	a relatively shallow, wide depression, the bottom of which usually has a continuous gradient */
    public const FEATURE_CODE_U_VLSU = 'VLSU'; /* valleys	a relatively shallow, wide depression, the bottom of which usually has a continuous gradient */

    /* */
    public const FEATURE_CODE_V_BUSH = 'BUSH'; /* bush(es)	a small clump of conspicuous bushes in an otherwise bare area */
    public const FEATURE_CODE_V_CULT = 'CULT'; /* cultivated area	an area under cultivation */
    public const FEATURE_CODE_V_FRST = 'FRST'; /* forest(s)	an area dominated by tree vegetation */
    public const FEATURE_CODE_V_FRSTF = 'FRSTF'; /* fossilized forest	a forest fossilized by geologic processes and now exposed at the earth's surface */
    public const FEATURE_CODE_V_GROVE = 'GROVE'; /* grove	a small wooded area or collection of trees growing closely together, occurring naturally or deliberately planted */
    public const FEATURE_CODE_V_GRSLD = 'GRSLD'; /* grassland	an area dominated by grass vegetation */
    public const FEATURE_CODE_V_GRVC = 'GRVC'; /* coconut grove	a planting of coconut trees */
    public const FEATURE_CODE_V_GRVO = 'GRVO'; /* olive grove	a planting of olive trees */
    public const FEATURE_CODE_V_GRVP = 'GRVP'; /* palm grove	a planting of palm trees */
    public const FEATURE_CODE_V_GRVPN = 'GRVPN'; /* pine grove	a planting of pine trees */
    public const FEATURE_CODE_V_HTH = 'HTH'; /* heath	an upland moor or sandy area dominated by low shrubby vegetation including heather */
    public const FEATURE_CODE_V_MDW = 'MDW'; /* meadow	a small, poorly drained area dominated by grassy vegetation */
    public const FEATURE_CODE_V_OCH = 'OCH'; /* orchard(s)	a planting of fruit or nut trees */
    public const FEATURE_CODE_V_SCRB = 'SCRB'; /* scrubland	an area of low trees, bushes, and shrubs stunted by some environmental limitation */
    public const FEATURE_CODE_V_TREE = 'TREE'; /* tree(s)	a conspicuous tree used as a landmark */
    public const FEATURE_CODE_V_TUND = 'TUND'; /* tundra	a marshy, treeless, high latitude plain, dominated by mosses, lichens, and low shrub vegetation under permafrost conditions */
    public const FEATURE_CODE_V_VIN = 'VIN'; /* vineyard	a planting of grapevines */
    public const FEATURE_CODE_V_VINS = 'VINS'; /* vineyards	plantings of grapevines */

    public const FEATURE_CODES_ALL = [

        /* A → country, state, region, ... */
        self::FEATURE_CLASS_A => [
            self::FEATURE_CODE_A_ADM1,
            self::FEATURE_CODE_A_ADM2,
            self::FEATURE_CODE_A_ADM3,
            self::FEATURE_CODE_A_ADM4,
        ],

        /* H → stream, lake, ... */
        self::FEATURE_CLASS_H => [],

        /* L → parks,area, ... */
        self::FEATURE_CLASS_L => [],

        /* P → city, village, ... */
        self::FEATURE_CLASS_P => [
            self::FEATURE_CODE_P_PPL,
            self::FEATURE_CODE_P_PPLA,
            self::FEATURE_CODE_P_PPLA2,
            self::FEATURE_CODE_P_PPLA3,
            self::FEATURE_CODE_P_PPLA4,
            self::FEATURE_CODE_P_PPLA5,
            self::FEATURE_CODE_P_PPLC,
            self::FEATURE_CODE_P_PPLCH,
            self::FEATURE_CODE_P_PPLF,
            self::FEATURE_CODE_P_PPLG,
            self::FEATURE_CODE_P_PPLH,
            self::FEATURE_CODE_P_PPLL,
            self::FEATURE_CODE_P_PPLQ,
            self::FEATURE_CODE_P_PPLR,
            self::FEATURE_CODE_P_PPLS,
            self::FEATURE_CODE_P_PPLW,
            self::FEATURE_CODE_P_PPLX,
            self::FEATURE_CODE_P_STLMT,
        ],

        /* R → road, railroad */
        self::FEATURE_CLASS_R => [],

        /* S → pot, building, farm */
        self::FEATURE_CLASS_S => [],

        /* T → mountain,hill,rock,... */
        self::FEATURE_CLASS_T => [],

        /* U → undersea */
        self::FEATURE_CLASS_U => [],

        /* V → forest,heath,... */
        self::FEATURE_CLASS_V => [],
    ];

    public const FEATURE_CODES_P_ADMIN_PLACES = [
        self::FEATURE_CODE_P_PPL,
        self::FEATURE_CODE_P_PPLA, // default
        self::FEATURE_CODE_P_PPLA2, // default
        self::FEATURE_CODE_P_PPLA3, // default
        self::FEATURE_CODE_P_PPLA4, // default
        self::FEATURE_CODE_P_PPLA5, // default
        self::FEATURE_CODE_P_PPLC, // default
        //self::FEATURE_CODE_P_PPLCH,
        self::FEATURE_CODE_P_PPLF,
        self::FEATURE_CODE_P_PPLG,
        //self::FEATURE_CODE_P_PPLH,
        self::FEATURE_CODE_P_PPLL,
        self::FEATURE_CODE_P_PPLQ,
        self::FEATURE_CODE_P_PPLR,
        self::FEATURE_CODE_P_PPLS,
        self::FEATURE_CODE_P_PPLW,
        self::FEATURE_CODE_P_PPLX,
        self::FEATURE_CODE_P_STLMT,
    ];

    public const FEATURE_CODES_P_DISTRICT_PLACES = [
        self::FEATURE_CODE_P_PPL,
        self::FEATURE_CODE_P_PPLX
    ];

    public const FEATURE_CODES_T_HILLS = [
        self::FEATURE_CODE_T_HLL,
        self::FEATURE_CODE_T_MT,
        self::FEATURE_CODE_T_MTS,
        self::FEATURE_CODE_T_PK,
        self::FEATURE_CODE_T_RK,
    ];
}
