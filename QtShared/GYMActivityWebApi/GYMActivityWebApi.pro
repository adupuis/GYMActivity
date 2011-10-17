#-------------------------------------------------
#
# Project created by QtCreator 2011-09-09T07:57:28
#
#-------------------------------------------------

QT       += network script scripttools dbus

QT       -= gui

TARGET = GYMActivityWebApi
TEMPLATE = lib

DEFINES += GYMACTIVITYWEBAPI_LIBRARY

SOURCES += gymactivitywebapi.cpp

HEADERS += gymactivitywebapi.h\
        GYMActivityWebApi_global.h

symbian {
    MMP_RULES += EXPORTUNFROZEN
    TARGET.UID3 = 0xED7367A1
    TARGET.CAPABILITY = 
    TARGET.EPOCALLOWDLLDATA = 1
    addFiles.sources = GYMActivityWebApi.dll
    addFiles.path = !:/sys/bin
    DEPLOYMENT += addFiles
}

unix:!symbian {
    maemo5 {
        target.path = /opt/usr/lib
    } else {
        target.path = /usr/lib
    }
    INSTALLS += target
}

OTHER_FILES += \
    qtc_packaging/debian_harmattan/rules \
    qtc_packaging/debian_harmattan/README \
    qtc_packaging/debian_harmattan/copyright \
    qtc_packaging/debian_harmattan/control \
    qtc_packaging/debian_harmattan/compat \
    qtc_packaging/debian_harmattan/changelog

