<?php

class School_Invoice extends CmfiveSeedMigration
{
    public $name = "School_Invoice";
    public $description = "Invoice template for mentoring sessions";

    public function seed()
    {
        ///////////////////
		//// Templates ////
		///////////////////

		$invoice_template = new Template($this->w);
		$invoice_template->title = "School Invoice Template";
		$invoice_template->module = "school";
		$invoice_template->category = "invoice";
		$invoice_template->is_active = 1;
		$invoice_template->template_body = '<html>

		<head>
			<meta content="text/html; charset=UTF-8" http-equiv="content-type">
			<style type="text/css">
				@import url("https://themes.googleusercontent.com/fonts/css?kit=Lx1xfUTR4qFjwg0Z_pb902LnpIA1imYKZeTuaOqvVDE");
		
				ol {
					margin: 0;
					padding: 0
				}
		
				table td,
				table th {
					
				}
			  .myPageWidth
			  {
				max-height: 100%;
				max-width: 100%;
			  }
			  
			  . myColor{
				border-color: white;
			  }
			  .myTopBorder {
				border-top-width: 5pt;
			  }
		
				.c18 {
					border-right-style: solid;
					/*padding: 2.8pt 2.8pt 2.8pt 2.7pt; */
					border-bottom-color: #000000;
					
					border-right-width: 1pt;
					border-left-color: #000000;
					/*vertical-align: top;*/
					border-right-color: #000000;
					border-left-width: 1pt;
					border-top-style: solid;
					border-left-style: solid;
					border-bottom-width: 1pt;
					/* width: 180pt; */
					border-top-color: #000000;
					border-bottom-style: solid
				}
		
				.c2 {
					border-right-style: solid;
					/*padding: 2.8pt 2.8pt 2.8pt 2.7pt;*/
					border-bottom-color: #000000;
					border-top-width: 1pt; /* changed */
					border-right-width: 1pt;
					border-left-color: #000000;
					vertical-align: top;
					border-right-color: #000000;
					border-left-width: 1pt;
					border-top-style: solid;
					border-left-style: solid;
					border-bottom-width: 1pt;
					/* width: 141pt; */
					border-top-color: #000000;
					border-bottom-style: solid
				}
		
				.c13 {
					border-right-style: solid;
					/*padding: 2.8pt 2.8pt 2.8pt 2.7pt;*/
					border-bottom-color: #000000;
					border-top-width: 1pt;/* changed */
					border-right-width: 1pt;
					border-left-color: #000000;
					vertical-align: top;
					border-right-color: #000000;
					border-left-width: 1pt;
					border-top-style: solid;
					border-left-style: solid;
					border-bottom-width: 1pt;
					/* width: 480.8pt; */
					border-top-color: #000000;
					border-bottom-style: solid
				}
		
				.c3 {
					border-right-style: solid;
					/*padding: 2.8pt 2.8pt 2.8pt 2.7pt;*/
					border-bottom-color: #000000;
					border-top-width: 1pt; /* changed */
					border-right-width: 1pt;
					border-left-color: #000000;
					vertical-align: top;
					border-right-color: #000000;
					border-left-width: 1pt;
					border-top-style: solid;
					border-left-style: solid;
					border-bottom-width: 1pt;
				   /* width: 159.8pt; */
					border-top-color: #000000;
					border-bottom-style: solid
				}
		
				.c0 {
					color: #000000;
					font-weight: 400;
					text-decoration: none;
					vertical-align: baseline;
					font-size: 15pt;
					font-family: "Times New Roman";
					font-style: normal
				}
		
				.c16 {
					color: #000000;
					font-weight: 400;
					text-decoration: none;
					vertical-align: baseline;
					font-size: 14pt;
					font-family: "Times New Roman";
					font-style: normal
				}
		
				.c14 {
					color: #000000;
					font-weight: 400;
					text-decoration: none;
					vertical-align: baseline;
					font-size: 16pt;
					font-family: "Times New Roman";
					font-style: normal
				}
		
				.c7 {
					color: #212121;
					font-weight: 400;
					text-decoration: none;
					vertical-align: baseline;
					font-size: 12pt;
					font-family: "Times New Roman";
					font-style: normal
				}
		
				.c21 {
					
					line-height: 1.15;
					orphans: 2;
					widows: 2;
					text-align: right
				}
		
				.c29 {
					background-color: #ffffff;
					font-size: 10pt;
					font-family: "Roboto";
					color: #222222;
					font-weight: 400
				}
		
				.c19 {
					color: #000000;
					text-decoration: none;
					vertical-align: baseline;
					font-family: "Times New Roman";
					font-style: normal
				}
		
				.c11 {
					font-size: 12pt;
					font-family: "Helvetica Neue";
					color: #222222;
					font-weight: 400
				}
		
				.c9 {
					margin-left: -2.7pt;
					border-spacing: 0;
					border-collapse: collapse;
					margin-right: auto
				}
			  .myTableBorder {
			  margin-right: 20%;
			  }
		
				.c1 {
					line-height: 1.0;
					text-align: right
				}
		
				.c22 {
					padding-top: 0pt;
					padding-bottom: 0pt;
					line-height: 1.15;
					text-align: left
				}
		
				.c5 {
					line-height: 1.0;
					text-align: left
				}
		
				.c24 {
					font-size: 12pt;
					font-style: normal;
					color: #000000;
					font-weight: 400
				}
		
				.c6 {
				   
					line-height: 1.0;
					text-align: right
				}
		
				.c12 {
					padding-top: 0pt;
					padding-bottom: 6pt;
					line-height: 1.0;
					text-align: left
				}
		
				.c23 {
					background-color: #ffffff;
					max-width: 481.9pt;
					padding: 56.7pt 56.7pt 56.7pt 56.7pt
				}
		
				.c28 {
					font-family: "Helvetica Neue";
					color: #222222;
					font-weight: 400
				}
		
				.c27 {
					text-decoration: none;
					vertical-align: baseline;
					font-style: normal
				}
		
				.c20 {
					font-weight: 400;
					font-size: 15pt
				}
		
				.c25 {
					font-weight: 400;
					font-size: 22pt
				}
		
				.c26 {
					font-size: 11pt;
					font-style: italic
				}
		
				.c15 {
					color: #212121
				}
		
				.c8 {
					height: 12pt
				}
		
				.c10 {
					font-size: 15pt
				}
		
				.c17 {
					font-weight: 700
				}
		
				.c4 {
					height: 0pt
				}
		
				.title {
					padding-top: 24pt;
					color: #000000;
					font-weight: 700;
					font-size: 36pt;
					padding-bottom: 6pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				.subtitle {
					padding-top: 18pt;
					color: #666666;
					font-size: 24pt;
					padding-bottom: 4pt;
					font-family: "Georgia";
					line-height: 1.0;
					page-break-after: avoid;
					font-style: italic;
					text-align: left
				}
			  .myInfo {
				  line-height: 0.5;
				text-align: right;
			  }
			  .myPadding {
				padding-top: 50pt;
				padding-bottom: 5pt;
			  }
		
				li {
					color: #000000;
					font-size: 12pt;
					font-family: "Times New Roman"
				}
		
				p {
					margin: 0;
					color: #000000;
					font-size: 12pt;
					font-family: "Times New Roman"
				}
		
				h1 {
					padding-top: 24pt;
					color: #000000;
					font-weight: 700;
					font-size: 24pt;
					padding-bottom: 6pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				h2 {
					padding-top: 18pt;
					color: #000000;
					font-weight: 700;
					font-size: 18pt;
					padding-bottom: 4pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				h3 {
					padding-top: 14pt;
					color: #000000;
					font-weight: 700;
					font-size: 14pt;
					padding-bottom: 4pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				h4 {
					padding-top: 12pt;
					color: #000000;
					font-weight: 700;
					font-size: 12pt;
					padding-bottom: 2pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				h5 {
					padding-top: 11pt;
					color: #000000;
					font-weight: 700;
					font-size: 11pt;
					padding-bottom: 2pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
		
				h6 {
					padding-top: 10pt;
					color: #000000;
					font-weight: 700;
					font-size: 10pt;
					padding-bottom: 2pt;
					font-family: "Times New Roman";
					line-height: 1.0;
					page-break-after: avoid;
					text-align: left
				}
			</style>
		</head>
		
		<body class="c23 doc-content">
			<p class="c5"><span
					style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 602.00px; height: 107.00px;"><img
						alt="" src="@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"
						style="width: 602.00px; height: 107.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"
						title=""></span></p>
			<p class="c5"><span class="c19 c25">Invoice</span></p>
			<p class="myInfo"><span class="c14">AU TO KNOW MENTORING</span></p>
			<p class="myInfo"><span class="c14">ABN: 98 277 208 482</span></p>
			<p class="myInfo"><span class="c19 c20">au.to.know.contact@gmail.com</span></p>
			<p class="myInfo"><span class="c19 c20">Invoice no. {{invoice_number}}</span></p>
			<p class="myInfo"><span class="c19 c20">date: {{date_time}}</span></p>
		  <p class="myInfo"><span class="c19 c20"></span></p>
			
		  
			
			<p class="c5" style="line-height: 0.5;"><span class="c24">Atten: </span><span class="c11"> </span><span class="c28">{{billing_contact_name}}</span></p>
			<p class="c5" style="line-height: 0.5;"><span class="c29">{{billing_contact_email}}</span></p>
			<p class="c12" style="line-height: 0.5;"><span class="c7">Service: Mentoring</span><span class="c15"> for {{student_name}}</span></p><a
				id="t.3a6d5b8aa4aa05e69fc323e6dbf98f38361fb35b"></a><a id="t.0"></a>
		 <table class="c9" border-collapse="collapse" cellpadding="3">
				<!--<tr class="c4">
					<td class="c13" colspan="3" rowspan="1">
						<p class="c1"><span class="c0">Hourly rate: </span><span class="c10">{{rate}}</span><span
								class="c0"> per </span><span class="c10">hour</span></p>
					</td>
				</tr>-->
				<tr class="c4">
					<td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c19 c10 c17">Item</span></p></td>
					<td class="c2" colspan="1" rowspan="1"><p class="c1"><span class="c19 c10 c17">Quantity</span></p></td>
					<td class="c2" colspan="1" rowspan="1"><p class="c1"><span class="c19 c10 c17">Rate</span></p></td>
					<td class="c3" colspan="1" rowspan="1"><p class="c1"><span class="c19 c10 c17">Totals</span></p></td>
				</tr>
			  {% for line in invoice_lines %}
				<tr class="c4 myTopBorder">
					<td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c10">{{line.line_item}} {{line.session_date}}</span></p></td>
					<td class="c2" colspan="1" rowspan="1"><p class="c1"><span class="c10">{{line.duration}}Hrs</span></p></td>
					  <td class="c2" colspan="1" rowspan="1"><p class="c1"><span class="c10">{{line.rate}}</span></p></td>
					<td class="c3" colspan="1" rowspan="1"><p class="c6"><span class="c10">{{line.total}}</span></p></td>
				</tr>
			  {% endfor %}
				<tr class="c4">
					<td class="c18" colspan="1" rowspan="1"><p class="c5"><span class="c19 c10 c17">Total Amount Payable</span></p></td>
					<td class="c2" colspan="2" rowspan="1"><p class="c1 c8"><span class="c10 c17 c19"></span></p></td>
					<td class="c3" colspan="1" rowspan="1"><p class="c6"><span class="c10 c17">${{invoice_total}}</span></p></td>
				</tr>
			</table>
			<p class="c5" style="line-height: 0.5;"><span class="c16">No GST has been charged</span></p>
			<p class="myInfo"><span class="c16">Name: Au To Know Mentoring </span></p>
			<p class="myInfo"><span class="c16">Account number: 12326235</span></p>
			<p class="myInfo"><span class="c16">BSB: 313140</span></p>
			<p class="c21"><span class="c26">Payment due within 7 days of date of  invoice</span></p>
		</body>
		</html>';

		$invoice_template->insert();
        
    }

	

}
