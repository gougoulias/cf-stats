# cf-stats

This is a wordpress plugin that creates statistics and view charts with canvasjs, from data that's been submited by contact form 7 plugin and collected by flamingo plugin
<hr>
<h3>Instructions:</h3>
Upload the zip into the Wordpress plugins folder or install plugin through the plugin installation page create a page for the results and add the shortcode:</p>
<p>[cf-stats name="HERE YOU MUST ENTER THE CF7 CONTACT FORM NAME" stats="HERE YOU MUST ADD THE CF7 FIELDS YOU WANT TO DISPLAY" group="HERE YOU MAY ADD CF7 FIELDS YOU WANT TO MAKE GROUPS OF" excludezero="YES" percentage="YES"]</p>

<H3>Important Notice:</h3> 
<p>the NAME and STATS shortcode parameters are required.</p>
<p>In the STATS parameter you MUST add comma seperated the contact form values you want to count, you can optional use the character "|" to give the chart another title that the contact form 7 field, any typography error couldn't generate the charts. </p>
<p>The GROUP shortcode parameter is optional and its used to make groups of answered question based the contact form 7 fields and answers.</p>
<p>The EXCLUDEZERO shortcode parameter is optional, and it's purpose is not to show the answers that have not been counted, the only acceptable value is "yes" otherwise is considered as "no" and the zero counted values will be vissible<p>
<p>The PERCENTAGE shortcode parameter is optional, and it's purpose is to show the answers with percentage value depending the number of answers, the only acceptable value is "yes" otherwise is considered as "no"<p>
<hr>
<p>Example of use : [cf-stats name="Gallop" stats="question1|This is the question 1,question2,question3,question4" group="gender,age" excludezero="yes" percentage="yes"]</p>
<hr>
<p>From Version 4 there is Cache option added</p>
