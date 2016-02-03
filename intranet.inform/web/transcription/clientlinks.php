<html>
<head>
	<style>
	* { target-new: tab ! important }
	a {color: blue };
	</style>
</head>
<body>
<?php
if (isset($_REQUEST{'client'})) makepage($_REQUEST{'client'});
	else echo "NO CLIENT SELECTED!";

function makepage($PageName) {
	switch ($PageName) {
		case 'BRISTOL':
		case 'brien':
			echo "<p style='text-align: right;'>BRISTOL</p>";
			pageadd('Abandoned Vehicle','http://www.bristol.gov.uk/abandoned-vehicle-reporting-system');
			pageadd('2.1 Flytipping','https://www.bristol.gov.uk/forms/fly-tipping#step1');
			pageadd('2.2.1 Graffiti','https://www.bristol.gov.uk/forms/graffiti-fly-posting#step1');
			pageadd('2.2.2 Flyposting','https://www.bristol.gov.uk/forms/graffiti-fly-posting#step1');
			pageadd('2.3.1  Dog Fouling','https://www.bristol.gov.uk/forms/street-cleansing#step1');
			pageadd('2.3.2 Bin not emptied','https://www.bristol.gov.uk/forms/street-cleansing#step1');
			pageadd('2.4.1 Dead animals','https://www.bristol.gov.uk/forms/street-cleansing#step1');
			pageadd('2.5 Sex and Drugs litter','https://www.bristol.gov.uk/forms/street-cleansing#step1');
			pageadd('2.6 Abandoned/Untaxed Vehicles (OOH only)','http://www.bristol.gov.uk/abandoned-vehicle-reporting-system');
			pageadd('2.8 Other street cleansing issues_Litter or overflowing litter binsglass or broken glass
			_blood_Human excrement or vomit_Fuel, oil and chemicals_Wet paint,_Dry paint_Leaves or blossom
			_Weeds','https://www.bristol.gov.uk/forms/street-cleansing#step1');
			pageadd('3.2 Street lighting','https://www.bristol.gov.uk/streets-travel/street-lights-report-a-fault');
			pageadd('3.3 Blocked gully, damaged road or fault on highway','https://www.bristol.gov.uk/streetfault/');
			pageadd('3.6. Damaged street name plates','https://www.bristol.gov.uk/streetfault/');
			break;
		case 'hpeen':
			echo "HIGH PEAK<br>";
			pageadd('GRAFFITI AND FLYPOSTING','http://www.highpeak.gov.uk/hp/council-services/street-care-and-cleaning/reporting-graffiti-and-flyposting');
			pageadd('MISSED COLLECTION/ BIN DELIVERIES','http://www.highpeak.gov.uk/hp/council-services/report-request-or-pay/report-a-bin-collection-problem');
			pageadd('BULKY COLLECTION','http://www.highpeak.gov.uk/hp/council-services/request-a-bulky-item-collection/request-a-bulky-itemelectrical-collection');
			pageadd('STREET CLEANING','http://www.highpeak.gov.uk/hp/council-services/street-care-and-cleaning/street-cleaning-request');
			pageadd('FLY TIPPING','http://www.highpeak.gov.uk/hp/council-services/street-care-and-cleaning/reporting-fly-tipping');
			pageadd('NOISE, NUISANCE AND ANTI SOCIAL BEHAVIOUR','http://www.highpeak.gov.uk/hp/council-services/pollution-and-noise-control/report-noise-anti-social-behaviour-other-nuisance');
			pageadd('FOOD HYGIENE ISSUES','http://www.highpeak.gov.uk/hp/council-services/food-safety/report-a-food-hygiene-issue');
			pageadd('ABANDONED VEHICLE','http://www.highpeak.gov.uk/hp/council-services/street-care-and-cleaning/report-an-abandoned-vehicle');
			pageadd('DOG FOULING','http://www.highpeak.gov.uk/hp/council-services/street-care-and-cleaning/reporting-dog-fouling');
			break;
		case 'smoen':
			echo "STAFFS MOORLANDS<br>";
			pageadd('GRAFFITI AND FLYPOSTING','http://www.staffsmoorlands.gov.uk/sm/council-services/street-care-and-cleaning/reporting-graffiti-and-flyposting');
			pageadd('MISSED COLLECTION/ BIN DELIVERIES','http://www.staffsmoorlands.gov.uk/sm/council-services/report-request-or-pay/report-a-bin-collection-problem');
			pageadd('STREET CLEANING','http://www.staffsmoorlands.gov.uk/sm/council-services/street-care-and-cleaning/street-cleaning-request');
			pageadd('FLY TIPPING','http://www.staffsmoorlands.gov.uk/sm/council-services/street-care-and-cleaning/reporting-fly-tipping');
			pageadd('NOISE, NUISANCE AND ANTI SOCIAL BEHAVIOUR','http://www.staffsmoorlands.gov.uk/sm/council-services/pollution-and-noise-control/report-noise-anti-social-behaviour-other-nuisance');
			pageadd('FOOD HYGIENE ISSUES','http://www.staffsmoorlands.gov.uk/sm/council-services/food-safety/report-a-food-hygiene-issue');
			pageadd('ABANDONED VEHICLE','http://www.staffsmoorlands.gov.uk/sm/council-services/street-care-and-cleaning/report-an-abandoned-vehicle');
			pageadd('DOG FOULING','http://www.staffsmoorlands.gov.uk/sm/council-services/street-care-and-cleaning/reporting-dog-fouling');
			break;
	}
}

function pageadd($PageLabel, $PageLink) {
	$FixLabel=strtr($PageLabel, array(' '=>'&nbsp;', '_'=>' <br>&nbsp;&nbsp;-'));
	echo "<p style='text-align: right;'><a href='{$PageLink}' target='_blank'>{$FixLabel}</a></p>";
}

?>
</body>
</html>
