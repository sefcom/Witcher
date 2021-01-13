httpreqr: main.cc
	g++ -o $@ $^ $(shell pkg-config --libs --cflags libcurl)

.PHONY: clean
clean:
	rm -f httpreqr

