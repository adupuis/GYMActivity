#include <QtCore/QString>
#include <QtTest/QtTest>

class GYMActivityWebApiTest : public QObject
{
    Q_OBJECT

public:
    GYMActivityWebApiTest();

private Q_SLOTS:
    void initTestCase();
    void cleanupTestCase();
    void testCase1();
};

GYMActivityWebApiTest::GYMActivityWebApiTest()
{
}

void GYMActivityWebApiTest::initTestCase()
{
}

void GYMActivityWebApiTest::cleanupTestCase()
{
}

void GYMActivityWebApiTest::testCase1()
{
    QVERIFY2(true, "Failure");
}

QTEST_APPLESS_MAIN(GYMActivityWebApiTest);

#include "tst_gymactivitywebapitest.moc"
