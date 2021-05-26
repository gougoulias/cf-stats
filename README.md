# cf-stats

This is a wordpress plugin that creates statistics and view charts with canvasjs, from data that's been submited by contact form 7 plugin and collected by flamingo plugin
<hr>
<h3>Instructions:</h3>
Upload the zip into the Wordpress plugins folder or install plugin through the plugin installation page
create a page for the results and add the shortcode:</p>
<p>[cf-stats name="HERE YOU MUST ENTER THE CF7 CONTACT FOM NAME" stats="HERE YOU MUST ADD THE CF7 FIELDS YOU WANT TO DISPLAY" group="HERE YOU MAY ADD CF7 FIELDS YOU WANT TO MAKE GROUPS OF" excludezero="YES"]</p>

<p>NOTICE: the NAME and STATS shortcode parameters are required, the GROUP shortcode parameter is optional, the EXCLUDEZERO shortcode parameter is optional and the only acceptable value is "yes"<p>
<hr>
<p>Example of use : [cf-stats name="Gallop" stats="question1,question2,question3,question4" group="gender,age" excludezero="yes"]</p>
