<?php
namespace tpaksu\LaravelOTPLogin\Factories;

class ServiceFactory
{
    /**
     * The services array (from the configuration)
     *
     * @var array
     */
    private $services;

    /**
     * Class constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->services = config("otp.services", []);
    }

    /**
     * @param   string  $serviceName    Service key to return instance of
     *
     * @return  ServiceInterface|null
     */
    public function getService($serviceName)
    {
        if ($this->serviceExists($serviceName)) {
            return $this->getServiceInstance($serviceName);
        }
        return null;
    }

    /**
     * Checks if the given service name exists as a service
     *
     * @param   string  $serviceName  The service to check
     *
     * @return  bool                  If the service exists
     */
    private function serviceExists($serviceName)
    {
        return isset($this->services[$serviceName])
        && isset($this->services[$serviceName]["class"])
        && class_exists($this->services[$serviceName]["class"]);
    }

    /**
     * Gets the service class name
     *
     * @param   string  $serviceName  The service to return name of
     *
     * @return  string                The class name
     */
    private function getServiceClassName($serviceName)
    {
        return $this->services[$serviceName];
    }

    /**
     * Gets the service instance
     *
     * @param   string  $serviceName  The service name to instantiate
     *
     * @return  ServiceInterface      The service class instance
     */
    private function getServiceInstance($serviceName)
    {
        $class = $this->getServiceClassName($serviceName);
        return new $class();
    }
}
