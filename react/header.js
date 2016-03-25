var HeaderClass = React.createClass({
	
render: function() {
	
	return (
		<p className="title">
			BYU Family Migration Research
			<br />
			<sub className="header">
				Modeling Large-Scale Historical Migration Patterns Using Family History Records
			</sub>
			<div id="logout">
				<form action="BYUFMR.php" method="post">
					<input type="hidden" name="step" value="logout" />
					<button type="submit" value="Logout">Logout</button>
				</form>
			</div>
		</p>
		
	);
}
	
});

var header = React.createElement(HeaderClass);

ReactDOM.render(header,document.getElementById('headerR'));