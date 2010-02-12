/**
 * DataExchangeLocator.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis WSDL2Java emitter.
 */

package no.AltInn.webservices;

public class DataExchangeLocator extends org.apache.axis.client.Service implements no.AltInn.webservices.DataExchange {

    // Use to get a proxy class for DataExchangeSoap
    private final java.lang.String DataExchangeSoap_address = "https://altinn4.accenture.no/webservices/dataexchange.asmx";
//    private final java.lang.String DataExchangeSoap_address = "https://www.altinn.no/webservices/dataexchange.asmx";

    public java.lang.String getDataExchangeSoapAddress() {
        return DataExchangeSoap_address;
    }

    // The WSDD service name defaults to the port name.
    private java.lang.String DataExchangeSoapWSDDServiceName = "DataExchangeSoap";

    public java.lang.String getDataExchangeSoapWSDDServiceName() {
        return DataExchangeSoapWSDDServiceName;
    }

    public void setDataExchangeSoapWSDDServiceName(java.lang.String name) {
        DataExchangeSoapWSDDServiceName = name;
    }

    public no.AltInn.webservices.DataExchangeSoap getDataExchangeSoap() throws javax.xml.rpc.ServiceException {
       java.net.URL endpoint;
        try {
            endpoint = new java.net.URL(DataExchangeSoap_address);
        }
        catch (java.net.MalformedURLException e) {
            throw new javax.xml.rpc.ServiceException(e);
        }
        return getDataExchangeSoap(endpoint);
    }

    public no.AltInn.webservices.DataExchangeSoap getDataExchangeSoap(java.net.URL portAddress) throws javax.xml.rpc.ServiceException {
        try {
            no.AltInn.webservices.DataExchangeSoapStub _stub = new no.AltInn.webservices.DataExchangeSoapStub(portAddress, this);
            _stub.setPortName(getDataExchangeSoapWSDDServiceName());
            return _stub;
        }
        catch (org.apache.axis.AxisFault e) {
            return null;
        }
    }

    /**
     * For the given interface, get the stub implementation.
     * If this service has no port for the given interface,
     * then ServiceException is thrown.
     */
    public java.rmi.Remote getPort(Class serviceEndpointInterface) throws javax.xml.rpc.ServiceException {
        try {
            if (no.AltInn.webservices.DataExchangeSoap.class.isAssignableFrom(serviceEndpointInterface)) {
                no.AltInn.webservices.DataExchangeSoapStub _stub = new no.AltInn.webservices.DataExchangeSoapStub(new java.net.URL(DataExchangeSoap_address), this);
                _stub.setPortName(getDataExchangeSoapWSDDServiceName());
                return _stub;
            }
        }
        catch (java.lang.Throwable t) {
            throw new javax.xml.rpc.ServiceException(t);
        }
        throw new javax.xml.rpc.ServiceException("There is no stub implementation for the interface:  " + (serviceEndpointInterface == null ? "null" : serviceEndpointInterface.getName()));
    }

    /**
     * For the given interface, get the stub implementation.
     * If this service has no port for the given interface,
     * then ServiceException is thrown.
     */
    public java.rmi.Remote getPort(javax.xml.namespace.QName portName, Class serviceEndpointInterface) throws javax.xml.rpc.ServiceException {
        if (portName == null) {
            return getPort(serviceEndpointInterface);
        }
        String inputPortName = portName.getLocalPart();
        if ("DataExchangeSoap".equals(inputPortName)) {
            return getDataExchangeSoap();
        }
        else  {
            java.rmi.Remote _stub = getPort(serviceEndpointInterface);
            ((org.apache.axis.client.Stub) _stub).setPortName(portName);
            return _stub;
        }
    }

    public javax.xml.namespace.QName getServiceName() {
        return new javax.xml.namespace.QName("http://AltInn.no/webservices/", "DataExchange");
    }

    private java.util.HashSet ports = null;

    public java.util.Iterator getPorts() {
        if (ports == null) {
            ports = new java.util.HashSet();
            ports.add(new javax.xml.namespace.QName("DataExchangeSoap"));
        }
        return ports.iterator();
    }

}
