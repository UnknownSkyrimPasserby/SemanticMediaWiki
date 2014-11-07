<?php

namespace SMW\Tests\Util\Fixtures\Facts;

use SMW\Tests\Util\UtilityFactory;
use SMW\Tests\Util\Fixtures\Properties\AreaProperty;
use SMW\Tests\Util\Fixtures\Properties\TemperatureProperty;
use SMW\Tests\Util\Fixtures\Properties\PopulationProperty;
use SMW\Tests\Util\Fixtures\Properties\YearProperty;
use SMW\Tests\Util\Fixtures\Properties\PopulationDensityProperty;
use SMW\Tests\Util\Fixtures\Properties\CityCategory;
use SMW\Tests\Util\Fixtures\Properties\LocatedInProperty;
use SMW\Tests\Util\Fixtures\Properties\FoundedProperty;

use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\DataValueFactory;
use SMW\Subobject;
use SMW\SemanticData;

use RuntimeException;

/**
 * @license GNU GPL v2+
 * @since 2.1
 *
 * @author mwjames
 */
class BerlinFactsheet {

	/**
	 * @var DIWikiPage
	 */
	private $targetSubject = null;

	/**
	 * @var DataValueFactory
	 */
	private $dataValueFactory;

	/**
	 * @since 2.1
	 *
	 * @param DIWikiPage|null $targetSubject
	 */
	public function __construct( DIWikiPage $targetSubject = null ) {
		$this->targetSubject = $targetSubject;

		if ( $this->targetSubject === null ) {
			$this->targetSubject = $this->asSubject();
		}

		$this->dataValueFactory = DataValueFactory::getInstance();
	}

	/**
	 * @since 2.1
	 *
	 * @param DIWikiPage $targetSubject
	 */
	public function setTargetSubject( DIWikiPage $targetSubject ) {
		$this->targetSubject = $targetSubject;
	}

	/**
	 * @since 2.1
	 *
	 * @return DIWikiPage
	 */
	public function asSubject() {
		return new DIWikiPage( 'Berlin', NS_MAIN );
	}

	/**
	 * @since 2.1
	 *
	 * @return SemanticData
	 */
	public function asEntity() {

		$semanticData = new SemanticData( $this->asSubject() );
		$semanticData->addDataValue( $this->getAreaValue() );
		$semanticData->addDataValue( $this->getAverageHighTemperatureValue() );
		$semanticData->addDataValue( $this->getPopulationValue() );
		$semanticData->addDataValue( $this->getPopulationDensityValue() );
		$semanticData->addDataValue( $this->getLocatedInValue() );
		$semanticData->addDataValue( $this->getFoundedValue() );
		$semanticData->addSubobject( $this->getDemographics() );

		$cityCategory = new CityCategory();

		$semanticData->addDataValue( $cityCategory->getCategoryValue() );

		return $semanticData;
	}

	/**
	 * @since 2.1
	 *
	 * @return DataValue
	 */
	public function getLocatedInValue() {

		$locatedInProperty = new LocatedInProperty();

		return $this->dataValueFactory->newDataItemValue(
			new DIWikiPage( 'Germany', NS_MAIN ),
			$locatedInProperty->getProperty()
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Berlin
	 *
	 * @return DataValue
	 */
	public function getAreaValue() {

		$areaProperty = new AreaProperty();

		return $this->dataValueFactory->newPropertyObjectValue(
			$areaProperty->getProperty(),
			'891.85 km²'
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Berlin
	 *
	 * @return DataValue
	 */
	public function getFoundedValue() {

		$foundedProperty = new FoundedProperty();

		return $this->dataValueFactory->newPropertyObjectValue(
			$foundedProperty->getProperty(),
			'1237'
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Berlin
	 *
	 * @return DataValue
	 */
	public function getAverageHighTemperatureValue() {

		$temperatureProperty = new TemperatureProperty();

		return $this->dataValueFactory->newPropertyObjectValue(
			$temperatureProperty->getProperty(),
			'13.4 °C',
			'Average high temperature'
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Berlin
	 *
	 * @return DataValue
	 */
	public function getPopulationValue() {

		$populationProperty = new PopulationProperty();

		return $this->dataValueFactory->newPropertyObjectValue(
			$populationProperty->getProperty(),
			'3517424'
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Demographics_of_Berlin
	 *
	 * @return DataValue
	 */
	public function getPopulationDensityValue() {

		if ( $this->targetSubject === null ) {
			throw new RuntimeException( 'Expected a target subject' );
		}

		$populationDensityProperty = new PopulationDensityProperty();

		return $this->dataValueFactory->newPropertyObjectValue(
			$populationDensityProperty->getProperty(),
			'3900;1 km²',
			'Population density',
			$this->targetSubject
		);
	}

	/**
	 * @since 2.1
	 *
	 * @see https://en.wikipedia.org/wiki/Demographics_of_Berlin
	 *
	 * @return Subobject
	 */
	public function getDemographics() {

		if ( $this->targetSubject === null ) {
			throw new RuntimeException( 'Expected a target subject' );
		}

		$subobject = new Subobject( $this->targetSubject->getTitle() );
		$subobject->setEmptyContainerForId( 'Berlin#Demographics' );

		$yearProperty = new YearProperty();

		$yearDataValue = $this->dataValueFactory->newPropertyObjectValue(
			$yearProperty->getProperty(),
			'2013'
		);

		$subobject->addDataValue( $yearDataValue );
		$subobject->addDataValue( $this->getAreaValue() );
		$subobject->addDataValue( $this->getPopulationValue() );
		$subobject->addDataValue( $this->getPopulationDensityValue() );

		return $subobject;
	}

	/**
	 * @since 2.1
	 */
	public function purge() {

		$subjects = array();

		$subjects[] = $this->asSubject();
		$subjects[] = $this->targetSubject;
		$subjects[] = $this->getFoundedValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getAreaValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getAverageHighTemperatureValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getPopulationValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getPopulationDensityValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getDemographics()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getLocatedInValue()->getProperty()->getDiWikiPage();
		$subjects[] = $this->getLocatedInValue()->getDataItem();

		// Record type needs extra attention
		$dataItems = $this->getPopulationDensityValue()->getDataItems();

		foreach ( $dataItems as $dataItem ) {
			$subjects[] = $dataItem;
		}

		// Clean-up subobject
		$properties = $this->getDemographics()->getSemanticData()->getProperties();

		foreach ( $properties as $property ) {
			$subjects[] = $property->getDiWikiPage();
		}

		$pageDeleter = UtilityFactory::getInstance()->newPageDeleter();

		foreach ( $subjects as $subject ) {
			if ( $subject instanceOf DIWikiPage ) {
				$pageDeleter->deletePage( $subject->getTitle() );
			}
		}
	}

}
