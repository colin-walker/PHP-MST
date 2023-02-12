<?xml version="1.0" ?>
<xsl:stylesheet version="3.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="/rss/channel/title"/> Web Feed</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <style type="text/css">
            
            html{
                font-family: sans-serif;
            	font-size: 16px;
            }
            
            body {
                margin:0;
            }
            
            article, details, header, main, menu, nav, section {
                display:block;
            }
            
            header {
                margin-bottom: 40px;   
            }
            
            a {
                color: #1B597B;   
            }
            
            h1 {
                font-size: 170%;
                margin-block-end: 0;
            }
            
            h2 {
                font-size: 150%;
            }
            
            h3 {
                font-size: 140%;
                margin-block-end: 0em;
            }
            
            .bg-yellow {
                background-color: #fff5b1 !important;
                padding: 10px 15px;
            }
                    
            .container-md {
                max-width: 768px;
                margin-right: auto;
                margin-left: auto;
            }
            
            @media screen and (max-width: 767px) {
                .container-md {
                    max-width: 90%;
                }
                
                html {
                    font-size: 18x;
                }
            }
        </style>
      </head>
      <body>
        <nav class="container-md markdown-body">
            <p class="bg-yellow">
                This is an <strong>RSS feed</strong>. <strong>Subscribe</strong> by copying the URL from the address bar into your newsreader.
                <br/>
                For more information visit <a href="https://aboutfeeds.com/">aboutfeeds.com</a>
            </p>
        </nav>
        <div class="container-md markdown-body">
          <header>
            <h1 class="border-0">
              <xsl:value-of select="/rss/channel/title"/>
            </h1>
            <p><xsl:value-of select="/rss/channel/description"/></p>
            <a class="head_link">
              <xsl:attribute name="href">
                <xsl:value-of select="/rss/channel/link"/>
              </xsl:attribute>
              <strong>Back to PHP-MST</strong>
            </a>
          </header>
          <h2>Recent Items</h2>
          <xsl:for-each select="/rss/channel/item">
            <div style="border-bottom: 1px dotted #999; margin-bottom: 35px; padding-bottom: 10px;">
              <h3>
                <a target="_blank" style="text-decoration: none;">
                  <xsl:attribute name="href">
                    <xsl:value-of select="link"/>
                    </xsl:attribute>
                    <xsl:choose>
                        <xsl:when test="title!=''">
                            <span style="margin-bottom: font-size: 150%;"><xsl:value-of select="title"/></span>
                        </xsl:when>
                        <xsl:otherwise>  
                            <span style="float: left; margin-right: 10px; margin-bottom: 0; line-height: 24px; font-size: 100% !important;">#</span>
                        </xsl:otherwise>
                    </xsl:choose>
                </a>
              </h3>
              <div>
                <xsl:value-of select="description" disable-output-escaping="yes" />
              </div>
            </div>
          </xsl:for-each>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>