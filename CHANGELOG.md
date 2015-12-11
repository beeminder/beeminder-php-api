# Change Log
All notable changes to beeminder-php-api will be documented in this file.  
The Change log format is documented at [http://keepachangelog.com/](http://keepachangelog.com/).  
This project adheres to [Semantic Versioning](http://semver.org/).  

## [1.1.0] - 2015-12-10

### Changed
- createDatapoint() now only takes 3 parameters, $slug, $value & $comment (optional)

### Fixed
- createDatapoint() no longer converts the value to an integer.

### Added
- createDatapointAdvanced() use this if you want to pass other parameters (like timestamp or requestid)
- Beeminder_Api_Goal->updatableGoalParameters property which you can use to specify which properties of a goal are selected as parameters to updateGoal
- CHANGELOG.md to better track changes.
