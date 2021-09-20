# ReplayWeb.page Player #
Adds a field formatter to play web archive files using ReplayWeb.page. 

## Setup and Usage ## 
### Requirements ###
- Drupal core media module
- Website should use HTTPS (required for the service worker)  

### Installation ###
Download and enable module using file upload, Drush, or Composer

### Configuration ###
1. Set the File field display of Web Archive media type to ReplayWebPage formatter on Structure › Media Types › Web Archive
› Manage display
2. Add or modify a media field in a content type and enable Web Archive as a reference type

### Usage ###
1. Add media at Content › Media
2. Upload web archive file and set Base URL if applicable
3. Attach to content

## Acknowledgements ##
Some of the code in this repository is modified code based on [Strawberry Field](https://github.com/esmero/strawberryfield)'s implementation of a similar player. 
