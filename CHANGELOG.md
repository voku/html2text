# Changelog

### 4.0.0 (19.11.2017)
* [!]: "php": ">=7.0" 
  * drop support for PHP < 7.0
  * use "strict_types"
  
* [!]: removed the legacy construct (take a look at the phpdoc, if needed)  
  
* [!]: removed deprecated methods
  * set_base_url() -> setBaseUrl()
  * set_html() -> setHtml()
  * get_text() -> getText() 
  * p() && print_text() -> echo getText()