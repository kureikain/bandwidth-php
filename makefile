VERSION = 0.2.0
SOURCE_DIR = $(shell pwd)
OUT_DIR=/tmp/bandwidth-api-kurei
BUILD_DIR=/tmp/bandwidth-api

all: clean gen

gen:
		mkdir $(OUT_DIR) 
		mkdir $(BUILD_DIR) 
		alpaca --no-python --no-node --no-ruby $(SOURCE_DIR)
		mv php "$(OUT_DIR)/"
	
clean:
		rm -rf python node php ruby $(OUT_DIR) $(BUILD_DIR)

build:
	cp -rf "$(SOURCE_DIR)" "$(BUILD_DIR)"
	cd $(BUILD_DIR) ; \
		git checkout master && \ 
	  cp -rf $(OUT_DIR)/* $(BUILD_DIR) && \
		rm -rf $(OUT_DIR) && \
		cp -rf $(SOURCE_DIR)/example.md $(BUILD_DIR) && \
		git add . && \
		git commit -m "Deploy new package generate on $(shell date)" && \
		git push origin master && \
		git checkout alpaca 
	
