# Configuration <a id="module-enrollment-configuration"></a>

## Module Configuration  <a id="module-enrollment-configuration-module"></a>


| Option            | Required | Description                                                                                          |
|-------------------| -------- |------------------------------------------------------------------------------------------------------|
| IcingaWeb2 FQDN   | **yes**  | The fully qualified domain name of your IcingaWeb2 instance, this will be used in all urls           |
| IcingaWeb2 Port   | no  | The port of your IcingaWeb2 instance in case it differs from standard ports                          |
| IcingaWeb2 Scheme | **yes**       | Use http or https for url schemes                                                                    |
| Mail Subject      | **yes**       | The Subject of the enrollment mail                                                                   |
| Mail Sender       | **yes**       | The sender address for the enrollment mail                                                           |
| Mail Body         | **yes**       | The mail Body for the enrollment mail. It supports the following variables: %%FQDN%%, %%USERNAME%%, %%REGISTRATIONURL%% |

