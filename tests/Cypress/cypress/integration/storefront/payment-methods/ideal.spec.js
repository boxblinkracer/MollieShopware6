import Devices from "Services/Devices";
import Session from "Actions/utils/Session"
import Shopware from "Services/Shopware";
// ------------------------------------------------------
import ShopConfigurationAction from "Actions/admin/ShopConfigurationAction";
// ------------------------------------------------------
import PaymentAction from "Actions/storefront/checkout/PaymentAction";
import DummyBasketScenario from "Scenarios/DummyBasketScenario";


const devices = new Devices();
const session = new Session();
const shopware = new Shopware();

const configAction = new ShopConfigurationAction();
const paymentAction = new PaymentAction();


const testDevices = [devices.getFirstDevice()];

const scenarioDummyBasket = new DummyBasketScenario(1);


describe('iDEAL Issuers', () => {

    testDevices.forEach(device => {

        context(devices.getDescription(device), () => {

            beforeEach(() => {
                devices.setDevice(device);
                configAction.setupShop(true, false);
                session.resetBrowserSession();
            });

            it('Issuer List on payment selection page', () => {

                scenarioDummyBasket.execute();

                if (shopware.getVersion() >= 6.4) {
                    paymentAction.showAllPaymentMethods();
                } else {
                    paymentAction.openPaymentsModal();
                }

                paymentAction.selectPaymentMethod('iDEAL');
                paymentAction.selectIDealIssuer('bunq');
            })
        })
    })
})