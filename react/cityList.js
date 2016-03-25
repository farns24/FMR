
var CityList = React.createClass({
  render: function() {
    return (
	<div className="input-group hide_while_creating_projects">
		<span className="input-group-addon ">City</span>		
		<datalist id="cityList">
			// Alabama
			<option value="Abbeville" />
			<option value="Adamsville" />
			<option value="Addison" />
			<option value="Akron" />
			<option value="Alabaster" />
			<option value="Albertville" />
			<option value="Alexander City" />
			<option value="Aliceville" />
			<option value="Allgood" />
			<option value="Altoona" />
			<option value="Andalusia" />
			<option value="Anderson" />
			<option value="Anniston" />
			<option value="Arab" />
			<option value="Ardmore" />
			<option value="Argo" />
			<option value="Ariton" />
			<option value="Arley" />
			<option value="Ashford" />
			<option value="Ashland" />
			<option value="Ashville" />
			<option value="Athens" />
			<option value="Atmore" />
			<option value="Attalla" />
			<option value="Auburn" />
			<option value="Autaugaville" />
			<option value="Babbie" />
			<option value="Baileyton" />
			<option value="Bakerhill" />
			<option value="Banks" />
			<option value="Bay Minette" />
			<option value="Bayou La Batre" />
			<option value="Bear Creek" />
			<option value="Beatrice" />
			<option value="Beaverton" />
			<option value="Belk" />
			<option value="Birmingham" />
			<option value="Brewton" />
			<option value="Butler" />
			<option value="Camden" />
			<option value="Carrollton" />
			<option value="Centre" />
			<option value="Centreville" />
			<option value="Carrollton" />
			<option value="Chatom" />
			<option value="Clanton" />
			<option value="Clayton" />
			<option value="Columbiana"/>
			<option value="Cullman"/>
			<option value="Dadeville"/>
			<option value="Decatur"/>
			<option value="Dothan"/>
			<option value="Double Springs"/>
			<option value="Elba"/>
			<option value="Eutaw"/>
			<option value="Evergreen"/>
			<option value="Fayette"/>
			<option value="Florence"/>
			<option value="Fort Payne"/>
			<option value="Gadsden"/>
			<option value="Geneva" />
			<option value="Greensboro" />
			<option value="Greenville" />
			<option value="Grove Hill" />
			<option value="Guntersville" />
			<option value="Hamilton" />
			<option value="Hayneville" />
			<option value="Heflin" />
			<option value="Huntsville" />
			<option value="Jasper" />
			<option value="La Fayette" />
			<option value="Linden"/>
			<option value="Livingston"/>
			<option value="Luverne"/>
			<option value="Marion"/>
			<option value="Mobile"/>
			<option value="Monroeville"/>
			<option value="Montgomery"/>
			<option value="Moulton"/>
			<option value="Oneonta"/>
			<option value="Opelika"/>
			<option value="Ozark"/>
			<option value="Pell City"/>
			<option value="Phenix City"/>
			<option value="Prattville" />
			<option value="Rockford" />
			<option value="Russellville" />
			<option value="Scottsboro" />
			<option value="Selma" />
			<option value="Talladega" />
			<option value="Troy" />
			<option value="Tuscaloosa" />
			<option value="Tuscumbia" />
			<option value="Tuskegee"/>
			<option value="Union Springs"/>
			<option value="Vernon"/>
			<option value="Wedowee"/>
			<option value="Wetumpka"/>
			
			// Alaska
			<option value="Anchorage"/>
			<option value="Fairbanks"/>
			<option value="Juneau"/>
			<option value="Badger"/>
			<option value="Knik-Fairview"/>
			<option value="Sitka"/>
			<option value="Wrangell"/>
			<option value="Ketchikan"/>
			<option value="Wasilla"/>
			<option value="Kenai"/>
			<option value="Kodiak"/>
			<option value="Bethel"/>
			<option value="Palmer"/>
			<option value="Homer"/>
			<option value="Unalaska"/>
			<option value="Barrow"/>
			<option value="Soldotna"/>
			<option value="Valdez"/>
			<option value="Nome"/>
			<option value="Kotzebue"/>
			<option value="Kotzebue"/>
			<option value="Seward"/>
			<option value="Dillingham"/>
			<option value="Cordova"/>
			<option value="North Pole"/>
			
			// Arizona 
			<option value="Bisbee"/>
			<option value="Clifton"/>
			<option value="Flagstaff"/>
			<option value="Florence"/>
			<option value="Globe"/>
			<option value="Holbrook"/>
			<option value="Kingman"/>
			<option value="Nogales"/>
			<option value="Parker"/>
			<option value="Phoenix"/>
			<option value="Prescott"/>
			<option value="Safford"/>
			<option value="St. Johns"/>
			<option value="Tucson"/>
			<option value="Yuma"/>
	
			//Arkansas
			<option value="Little Rock" />
			<option value="Fayetteville" />
			<option value="Hot Springs" />
			<option value="Fort Smith" />
			<option value="Bentonville" />
			<option value="Jonesboro" />
			<option value="Conway" />


			// Florida
			<option value="Apalachicola" />
			<option value="Arcadia" />
			<option value="Bartow" />
			<option value="Blountstown"/>
			<option value="Bonifay" />
			<option value="Bradenton" />
			<option value="Bristol" />
			<option value="Bronson" />
			<option value="Brooksville" />
			<option value="Bunnell" />
			<option value="Bushnell" />
			<option value="Bushnell" />
			<option value="Bushnell" />
			<option value="Crestview" />
			<option value="Cross City" />
			<option value="Cross City" />
			<option value="DeFuniak Springs" />
			<option value="DeLand"/>
			<option value="Fernandina Beach"/>
			<option value="Fort Lauderdale"/>
			<option value="Fort Myers"/>
			<option value="Fort Pierce"/>
			<option value="Gainesville"/>
			<option value="Green Cove Springs"/>
			<option value="Inverness"/>
			<option value="Jacksonville"/>
			<option value="Jasper"/>
			<option value="Key West"/>
			<option value="Kissimmee"/>
			<option value="LaBelle"/>
			<option value="Lake Butler"/>
			<option value="Lake City"/>
			<option value="Live Oak"/>
			<option value="Macclenny"/>
			<option value="Madison"/>
			<option value="Marianna"/>
			<option value="Mayo"/>
			<option value="Miami"/>
			<option value="Milton"/>
			<option value="Monticello"/>
			<option value="Moore Haven"/>
			<option value="Naples"/>
			<option value="Ocala"/>
			<option value="Okeechobee"/>
			<option value="Orlando"/>
			<option value="Palatka"/>
			<option value="Panama City"/>
			<option value="Pensacola"/>
			<option value="Perry"/>
			<option value="Port St. Joe"/>
			<option value="Punta Gorda"/>
			<option value="Quincy"/>
			<option value="Sanford"/>
			<option value="Sarasota"/>
			<option value="Sebring"/>
			<option value="St. Augustine"/>
			<option value="Starke"/>
			<option value="Stuart"/>
			<option value="Tallahassee"/>
			<option value="Tampa"/>
			<option value="Tavares"/>
			<option value="Titusville"/>
			<option value="Trenton"/>
			<option value="Vero Beach"/>
			<option value="Wauchula"/>
			<option value="West Palm Beach"/>
			
			// Vermont 
			<option value="Montpelier" />
			<option value="Burlington" />
			<option value="Winooski" />
			<option value="South Burlington"/>
			<option value="Rutland" />
			<option value="Barre" />
			<option value="St. Albans" />
			<option value="Newport" />
			<option value="Vergennes" />
			<option value="Colchester" />
			<option value="Bennington" />
			<option value="Brattleboro" />
			<option value="Milton" />
			<option value="Hartford" />
			<option value="Springfield" />
			<option value="Williston" />
			<option value="Middlebury" />
			<option value="St. Johnsbury"/>
		</datalist>
		<input className="form-control" type="text" list="cityList" name="city" placeholder="*City" id="autocomplete"/>		
	</div> 
	);
  }
});

var cityList_el = React.createElement(CityList);

ReactDOM.render(
  cityList_el,
  document.getElementById('cityListR')
);