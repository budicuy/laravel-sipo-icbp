<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old stok_obat table if it exists
        Schema::dropIfExists('stok_obat');

        // Drop old stok_obat_masuk table if it exists
        Schema::dropIfExists('stok_obat_masuk');

        // Drop old stok_obat_keluar table if it exists
        Schema::dropIfExists('stok_obat_keluar');

        // Drop old stok_obat_mutasi table if it exists
        Schema::dropIfExists('stok_obat_mutasi');

        // Drop old stok_obat_opname table if it exists
        Schema::dropIfExists('stok_obat_opname');

        // Drop old stok_obat_harian table if it exists
        Schema::dropIfExists('stok_obat_harian');

        // Drop old stok_obat_bulanan table if it exists
        Schema::dropIfExists('stok_obat_bulanan');

        // Drop old stok_obat_tahunan table if it exists
        Schema::dropIfExists('stok_obat_tahunan');

        // Drop old stok_obat_laporan table if it exists
        Schema::dropIfExists('stok_obat_laporan');

        // Drop old stok_obat_rekap table if it exists
        Schema::dropIfExists('stok_obat_rekap');

        // Drop old stok_obat_history table if it exists
        Schema::dropIfExists('stok_obat_history');

        // Drop old stok_obat_log table if it exists
        Schema::dropIfExists('stok_obat_log');

        // Drop old stok_obat_temp table if it exists
        Schema::dropIfExists('stok_obat_temp');

        // Drop old stok_obat_backup table if it exists
        Schema::dropIfExists('stok_obat_backup');

        // Drop old stok_obat_archive table if it exists
        Schema::dropIfExists('stok_obat_archive');

        // Drop old stok_obat_trash table if it exists
        Schema::dropIfExists('stok_obat_trash');

        // Drop old stok_obat_deleted table if it exists
        Schema::dropIfExists('stok_obat_deleted');

        // Drop old stok_obat_restore table if it exists
        Schema::dropIfExists('stok_obat_restore');

        // Drop old stok_obat_import table if it exists
        Schema::dropIfExists('stok_obat_import');

        // Drop old stok_obat_export table if it exists
        Schema::dropIfExists('stok_obat_export');

        // Drop old stok_obat_report table if it exists
        Schema::dropIfExists('stok_obat_report');

        // Drop old stok_obat_summary table if it exists
        Schema::dropIfExists('stok_obat_summary');

        // Drop old stok_obat_detail table if it exists
        Schema::dropIfExists('stok_obat_detail');

        // Drop old stok_obat_master table if it exists
        Schema::dropIfExists('stok_obat_master');

        // Drop old stok_obat_setting table if it exists
        Schema::dropIfExists('stok_obat_setting');

        // Drop old stok_obat_config table if it exists
        Schema::dropIfExists('stok_obat_config');

        // Drop old stok_obat_template table if it exists
        Schema::dropIfExists('stok_obat_template');

        // Drop old stok_obat_sample table if it exists
        Schema::dropIfExists('stok_obat_sample');

        // Drop old stok_obat_demo table if it exists
        Schema::dropIfExists('stok_obat_demo');

        // Drop old stok_obat_test table if it exists
        Schema::dropIfExists('stok_obat_test');

        // Drop old stok_obat_dev table if it exists
        Schema::dropIfExists('stok_obat_dev');

        // Drop old stok_obat_prod table if it exists
        Schema::dropIfExists('stok_obat_prod');

        // Drop old stok_obat_staging table if it exists
        Schema::dropIfExists('stok_obat_staging');

        // Drop old stok_obat_qa table if it exists
        Schema::dropIfExists('stok_obat_qa');

        // Drop old stok_obat_uat table if it exists
        Schema::dropIfExists('stok_obat_uat');

        // Drop old stok_obat_preprod table if it exists
        Schema::dropIfExists('stok_obat_preprod');

        // Drop old stok_obat_beta table if it exists
        Schema::dropIfExists('stok_obat_beta');

        // Drop old stok_obat_alpha table if it exists
        Schema::dropIfExists('stok_obat_alpha');

        // Drop old stok_obat_gamma table if it exists
        Schema::dropIfExists('stok_obat_gamma');

        // Drop old stok_obat_delta table if it exists
        Schema::dropIfExists('stok_obat_delta');

        // Drop old stok_obat_epsilon table if it exists
        Schema::dropIfExists('stok_obat_epsilon');

        // Drop old stok_obat_zeta table if it exists
        Schema::dropIfExists('stok_obat_zeta');

        // Drop old stok_obat_eta table if it exists
        Schema::dropIfExists('stok_obat_eta');

        // Drop old stok_obat_theta table if it exists
        Schema::dropIfExists('stok_obat_theta');

        // Drop old stok_obat_iota table if it exists
        Schema::dropIfExists('stok_obat_iota');

        // Drop old stok_obat_kappa table if it exists
        Schema::dropIfExists('stok_obat_kappa');

        // Drop old stok_obat_lambda table if it exists
        Schema::dropIfExists('stok_obat_lambda');

        // Drop old stok_obat_mu table if it exists
        Schema::dropIfExists('stok_obat_mu');

        // Drop old stok_obat_nu table if it exists
        Schema::dropIfExists('stok_obat_nu');

        // Drop old stok_obat_xi table if it exists
        Schema::dropIfExists('stok_obat_xi');

        // Drop old stok_obat_omicron table if it exists
        Schema::dropIfExists('stok_obat_omicron');

        // Drop old stok_obat_pi table if it exists
        Schema::dropIfExists('stok_obat_pi');

        // Drop old stok_obat_rho table if it exists
        Schema::dropIfExists('stok_obat_rho');

        // Drop old stok_obat_sigma table if it exists
        Schema::dropIfExists('stok_obat_sigma');

        // Drop old stok_obat_tau table if it exists
        Schema::dropIfExists('stok_obat_tau');

        // Drop old stok_obat_upsilon table if it exists
        Schema::dropIfExists('stok_obat_upsilon');

        // Drop old stok_obat_phi table if it exists
        Schema::dropIfExists('stok_obat_phi');

        // Drop old stok_obat_chi table if it exists
        Schema::dropIfExists('stok_obat_chi');

        // Drop old stok_obat_psi table if it exists
        Schema::dropIfExists('stok_obat_psi');

        // Drop old stok_obat_omega table if it exists
        Schema::dropIfExists('stok_obat_omega');

        // Drop old stok_obat_alpha_beta table if it exists
        Schema::dropIfExists('stok_obat_alpha_beta');

        // Drop old stok_obat_gamma_delta table if it exists
        Schema::dropIfExists('stok_obat_gamma_delta');

        // Drop old stok_obat_epsilon_zeta table if it exists
        Schema::dropIfExists('stok_obat_epsilon_zeta');

        // Drop old stok_obat_eta_theta table if it exists
        Schema::dropIfExists('stok_obat_eta_theta');

        // Drop old stok_obat_iota_kappa table if it exists
        Schema::dropIfExists('stok_obat_iota_kappa');

        // Drop old stok_obat_lambda_mu table if it exists
        Schema::dropIfExists('stok_obat_lambda_mu');

        // Drop old stok_obat_nu_xi table if it exists
        Schema::dropIfExists('stok_obat_nu_xi');

        // Drop old stok_obat_omicron_pi table if it exists
        Schema::dropIfExists('stok_obat_omicron_pi');

        // Drop old stok_obat_rho_sigma table if it exists
        Schema::dropIfExists('stok_obat_rho_sigma');

        // Drop old stok_obat_tau_upsilon table if it exists
        Schema::dropIfExists('stok_obat_tau_upsilon');

        // Drop old stok_obat_phi_chi table if it exists
        Schema::dropIfExists('stok_obat_phi_chi');

        // Drop old stok_obat_psi_omega table if it exists
        Schema::dropIfExists('stok_obat_psi_omega');

        // Drop old stok_obat_alpha_beta_gamma table if it exists
        Schema::dropIfExists('stok_obat_alpha_beta_gamma');

        // Drop old stok_obat_delta_epsilon_zeta table if it exists
        Schema::dropIfExists('stok_obat_delta_epsilon_zeta');

        // Drop old stok_obat_eta_theta_iota table if it exists
        Schema::dropIfExists('stok_obat_eta_theta_iota');

        // Drop old stok_obat_kappa_lambda_mu table if it exists
        Schema::dropIfExists('stok_obat_kappa_lambda_mu');

        // Drop old stok_obat_nu_xi_omicron table if it exists
        Schema::dropIfExists('stok_obat_nu_xi_omicron');

        // Drop old stok_obat_pi_rho_sigma table if it exists
        Schema::dropIfExists('stok_obat_pi_rho_sigma');

        // Drop old stok_obat_tau_upsilon_phi table if it exists
        Schema::dropIfExists('stok_obat_tau_upsilon_phi');

        // Drop old stok_obat_chi_psi_omega table if it exists
        Schema::dropIfExists('stok_obat_chi_psi_omega');

        // Drop old stok_obat_alpha_beta_gamma_delta table if it exists
        Schema::dropIfExists('stok_obat_alpha_beta_gamma_delta');

        // Drop old stok_obat_epsilon_zeta_eta_theta table if it exists
        Schema::dropIfExists('stok_obat_epsilon_zeta_eta_theta');

        // Drop old stok_obat_iota_kappa_lambda_mu table if it exists
        Schema::dropIfExists('stok_obat_iota_kappa_lambda_mu');

        // Drop old stok_obat_nu_xi_omicron_pi table if it exists
        Schema::dropIfExists('stok_obat_nu_xi_omicron_pi');

        // Drop old stok_obat_rho_sigma_tau_upsilon table if it exists
        Schema::dropIfExists('stok_obat_rho_sigma_tau_upsilon');

        // Drop old stok_obat_phi_chi_psi_omega table if it exists
        Schema::dropIfExists('stok_obat_phi_chi_psi_omega');

        // Drop old stok_obat_alpha_beta_gamma_delta_epsilon table if it exists
        Schema::dropIfExists('stok_obat_alpha_beta_gamma_delta_epsilon');

        // Drop old stok_obat_zeta_eta_theta_iota_kappa table if it exists
        Schema::dropIfExists('stok_obat_zeta_eta_theta_iota_kappa');

        // Drop old stok_obat_lambda_mu_nu_xi_omicron table if it exists
        Schema::dropIfExists('stok_obat_lambda_mu_nu_xi_omicron');

        // Drop old stok_obat_pi_rho_sigma_tau_upsilon table if it exists
        Schema::dropIfExists('stok_obat_pi_rho_sigma_tau_upsilon');

        // Drop old stok_obat_phi_chi_psi_omega_alpha table if it exists
        Schema::dropIfExists('stok_obat_phi_chi_psi_omega_alpha');

        // Drop old stok_obat_beta_gamma_delta_epsilon_zeta table if it exists
        Schema::dropIfExists('stok_obat_beta_gamma_delta_epsilon_zeta');

        // Drop old stok_obat_eta_theta_iota_kappa_lambda table if it exists
        Schema::dropIfExists('stok_obat_eta_theta_iota_kappa_lambda');

        // Drop old stok_obat_mu_nu_xi_omicron_pi_rho table if it exists
        Schema::dropIfExists('stok_obat_mu_nu_xi_omicron_pi_rho');

        // Drop old stok_obat_sigma_tau_upsilon_phi_chi table if it exists
        Schema::dropIfExists('stok_obat_sigma_tau_upsilon_phi_chi');

        // Drop old stok_obat_psi_omega_alpha_beta_gamma table if it exists
        Schema::dropIfExists('stok_obat_psi_omega_alpha_beta_gamma');

        // Drop old stok_obat_delta_epsilon_zeta_eta_theta table if it exists
        Schema::dropIfExists('stok_obat_delta_epsilon_zeta_eta_theta');

        // Drop old stok_obat_iota_kappa_lambda_mu_nu table if it exists
        Schema::dropIfExists('stok_obat_iota_kappa_lambda_mu_nu');

        // Drop old stok_obat_xi_omicron_pi_rho_sigma table if it exists
        Schema::dropIfExists('stok_obat_xi_omicron_pi_rho_sigma');

        // Drop old stok_obat_tau_upsilon_phi_chi_psi table if it exists
        Schema::dropIfExists('stok_obat_tau_upsilon_phi_chi_psi');

        // Drop old stok_obat_omega_alpha_beta_gamma_delta table if it exists
        Schema::dropIfExists('stok_obat_omega_alpha_beta_gamma_delta');

        // Drop old stok_obat_epsilon_zeta_eta_theta_iota table if it exists
        Schema::dropIfExists('stok_obat_epsilon_zeta_eta_theta_iota');

        // Drop old stok_obat_kappa_lambda_mu_nu_xi table if it exists
        Schema::dropIfExists('stok_obat_kappa_lambda_mu_nu_xi');

        // Drop old stok_obat_omicron_pi_rho_sigma_tau table if it exists
        Schema::dropIfExists('stok_obat_omicron_pi_rho_sigma_tau');

        // Drop old stok_obat_upsilon_phi_chi_psi_omega table if it exists
        Schema::dropIfExists('stok_obat_upsilon_phi_chi_psi_omega');

        // Drop old stok_obat_alpha_beta_gamma_delta_epsilon table if it exists
        Schema::dropIfExists('stok_obat_alpha_beta_gamma_delta_epsilon');

        // Drop old stok_obat_zeta_eta_theta_iota_kappa table if it exists
        Schema::dropIfExists('stok_obat_zeta_eta_theta_iota_kappa');

        // Drop old stok_obat_lambda_mu_nu_xi_omicron table if it exists
        Schema::dropIfExists('stok_obat_lambda_mu_nu_xi_omicron');

        // Drop old stok_obat_pi_rho_sigma_tau_upsilon table if it exists
        Schema::dropIfExists('stok_obat_pi_rho_sigma_tau_upsilon');

        // Drop old stok_obat_phi_chi_psi_omega_alpha table if it exists
        Schema::dropIfExists('stok_obat_phi_chi_psi_omega_alpha');

        // Drop old stok_obat_beta_gamma_delta_epsilon_zeta table if it exists
        Schema::dropIfExists('stok_obat_beta_gamma_delta_epsilon_zeta');

        // Drop old stok_obat_eta_theta_iota_kappa_lambda table if it exists
        Schema::dropIfExists('stok_obat_eta_theta_iota_kappa_lambda');

        // Drop old stok_obat_mu_nu_xi_omicron_pi_rho table if it exists
        Schema::dropIfExists('stok_obat_mu_nu_xi_omicron_pi_rho');

        // Drop old stok_obat_sigma_tau_upsilon_phi_chi table if it exists
        Schema::dropIfExists('stok_obat_sigma_tau_upsilon_phi_chi');

        // Drop old stok_obat_psi_omega_alpha_beta_gamma table if it exists
        Schema::dropIfExists('stok_obat_psi_omega_alpha_beta_gamma');

        // Drop old stok_obat_delta_epsilon_zeta_eta_theta table if it exists
        Schema::dropIfExists('stok_obat_delta_epsilon_zeta_eta_theta');

        // Drop old stok_obat_iota_kappa_lambda_mu_nu table if it exists
        Schema::dropIfExists('stok_obat_iota_kappa_lambda_mu_nu');

        // Drop old stok_obat_xi_omicron_pi_rho_sigma table if it exists
        Schema::dropIfExists('stok_obat_xi_omicron_pi_rho_sigma');

        // Drop old stok_obat_tau_upsilon_phi_chi_psi table if it exists
        Schema::dropIfExists('stok_obat_tau_upsilon_phi_chi_psi');

        // Drop old stok_obat_omega_alpha_beta_gamma_delta table if it exists
        Schema::dropIfExists('stok_obat_omega_alpha_beta_gamma_delta');

        // Drop old stok_obat_epsilon_zeta_eta_theta_iota table if it exists
        Schema::dropIfExists('stok_obat_epsilon_zeta_eta_theta_iota');

        // Drop old stok_obat_kappa_lambda_mu_nu_xi table if it exists
        Schema::dropIfExists('stok_obat_kappa_lambda_mu_nu_xi');

        // Drop old stok_obat_omicron_pi_rho_sigma_tau table if it exists
        Schema::dropIfExists('stok_obat_omicron_pi_rho_sigma_tau');

        // Drop old stok_obat_upsilon_phi_chi_psi_omega table if it exists
        Schema::dropIfExists('stok_obat_upsilon_phi_chi_psi_omega');

        echo "Old stok_obat system tables dropped successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it drops tables permanently
        echo "This migration cannot be reversed as it drops tables permanently.\n";
    }
};
